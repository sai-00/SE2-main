<?php
$postsFile = '../json/posts.json';
$dogsFile = '../json/dogs.json';

// Initialize posts and dogs data arrays
$postsData = [];
$dogsData = [];

// Load posts data if the file exists
if (file_exists($postsFile)) {
    $postsJson = file_get_contents($postsFile);
    $decodedPosts = json_decode($postsJson, true);
    if (is_array($decodedPosts)) {
        $postsData = $decodedPosts;
    }
}

// Load dogs data if the file exists
if (file_exists($dogsFile)) {
    $dogsJson = file_get_contents($dogsFile);
    $decodedDogs = json_decode($dogsJson, true);
    if (is_array($decodedDogs)) {
        $dogsData = $decodedDogs;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['text']) && isset($_FILES['image'])) {
        // Handle new post creation
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadFile = $uploadDir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

        // Validate uploaded image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            die("File is not an image.");
        }

        // Check file size (limit to 5MB)
        if ($_FILES['image']['size'] > 5000000) {
            die("Sorry, your file is too large.");
        }

        // Allow certain file formats
        if ($imageFileType !== 'jpg' && $imageFileType !== 'png' && $imageFileType !== 'jpeg' && $imageFileType !== 'gif') {
            die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Move uploaded file to destination
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            die("Sorry, there was an error uploading your file.");
        }

        // Create new post data
        $text = htmlspecialchars($_POST['text']);
        $tags = htmlspecialchars($_POST['tags']);
        $tagArray = array_map('trim', explode(',', $tags));
        $tagArray = array_map('strtolower', $tagArray);

        $postData = [
            'id' => uniqid(),
            'username' => $_SESSION['username'],
            'image' => $uploadFile,
            'text' => $text,
            'tags' => $tagArray,
            'comments' => []
        ];

        // Append new post data
        $postsData[] = $postData;

        // Save updated posts data back to JSON file
        file_put_contents($postsFile, json_encode($postsData, JSON_PRETTY_PRINT));
    } elseif (isset($_POST['comment_post_id']) && isset($_POST['comment_text'])) {
        // Handle adding a comment
        $commentPostId = htmlspecialchars($_POST['comment_post_id']);
        $commentText = htmlspecialchars($_POST['comment_text']);

        // Read existing posts
        if (file_exists($postsFile)) {
            $postsJson = file_get_contents($postsFile);
            $postsData = json_decode($postsJson, true);
        }

        // Find the post and add comment
        foreach ($postsData as &$post) {
            if ($post['id'] === $commentPostId) {
                $comment = [
                    'username' => $_SESSION['username'],
                    'text' => $commentText
                ];
                $post['comments'][] = $comment;
                break;
            }
        }

        // Save updated posts data back to JSON file
        file_put_contents($postsFile, json_encode($postsData, JSON_PRETTY_PRINT));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Home</title>
    <link rel="stylesheet" href="../css/site_layout.css">
    <link rel="stylesheet" href="../css/modal.css">
    <style>
        .search-form {
            background-color: #dba181;
            width: 55%;
            max-width: 15%;
            padding-top: 20px;
            padding-left: 20px;
            padding-right: 40px;
        }

        .search-form button {
            padding: 10px;
            text-align: center;
            width: 50%;
        }

        #advanced_search {
            padding: 10px;
            border-radius: 20px;
            margin-bottom: 8px;
        }

        .post-details p.text-preview {
            display: inline-block;
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <nav class="top-navbar">
        <div class="logo">
            <a href="landing.php"><strong>Pawpedia</strong></a>
        </div>

        <div class="nav-links">
            <a href="signup.php">Signup</a>
            <a href="login.php">Login</a>
        </div>
    </nav>

    <section class="main-content">

        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <img id="modalImage" src="" alt="Modal Image">
                <p id="modalText"></p>
                <p id="modalUsername"></p>
                <p id="modalTags"></p>
                <br><hr>
                <h3>Comments</h3>
                <div id="commentsSection"></div>
                <input type="hidden" name="comment_post_id" id="commentPostId">
            </div>
        </div>

        <div class="search-form">
            <div class="search-bar">
                <label for="search">Search a breed:</label><br><br>
                <select id="search" name="search" onchange="searchPosts()">
                    <option value="">Select a breed</option>
                    <option value="Shih Tzu">Shih Tzu</option>
                    <option value="Shiba Inu">Shiba Inu</option>
                    <option value="Pug">Pug</option>
                    <option value="Corgi">Corgi</option>
                    <option value="Beagle">Beagle</option>
                    <option value="Yorkshire">Yorkshire</option>
                    <option value="Pomeranian">Pomeranian</option>
                    <option value="Poodle">Poodle</option>
                    <option value="Bulldog">Bulldog</option>
                    <option value="Golden Retriever">Golden Retriever</option>
                    <option value="Labrador">Labrador</option>
                    <option value="Borzoi">Borzoi</option>
                    <option value="Dalmatian">Dalmatian</option>
                    <option value="Chihuahua">Chihuahua</option>
                    <option value="Husky">Husky</option>
                </select>
                <br>
                <button onclick="clearBreedSearch()">Clear Breed Search</button>
            </div>
            <br><br>
            <label for="advanced_search">Advanced Search:</label> <br><br>
            <input type="text" id="advanced_search" placeholder="Search keywords (i.e. loud)">
            <br><br>
            <button onclick="clearAdvancedSearch()">Clear Advanced Search</button>
            <br><br>
            <div id="wiki-entry" class="wiki-entry"></div>
        </div>

        <div class="posts">
            <div class='tiles' id='post-tiles'>
            <?php
            if (file_exists($postsFile) || file_exists($dogsFile)) {
                $posts = file_exists($postsFile) ? array_reverse(json_decode(file_get_contents($postsFile), true)) : [];
                $dogs = file_exists($dogsFile) ? json_decode(file_get_contents($dogsFile), true) : [];

                $searchTag = isset($_GET['search']) ? strtolower(htmlspecialchars($_GET['search'])) : '';

                // Display wiki entry
                if (!empty($searchTag)) {
                    foreach ($dogs as $dog) {
                        if (strtolower($dog['breed']) === $searchTag) {
                            echo "<div class='post-tile' style='background-color: #dba181;'>";
                            echo "<img src='" . htmlspecialchars($dog['image']) . "' alt='Dog Image'>";
                            echo "<div class='post-details'>";
                            echo "<p><strong><u>Wiki Entry!</u></strong></p>";
                            echo "<p><strong>Breed:</strong> " . htmlspecialchars($dog['breed']) . "</p>";
                            echo "<br><br>";
                            echo "<p>Click 'read more' to redirect to " . htmlspecialchars($dog['breed']) . " information.</p>";
                            echo "<button onclick=\"window.location.href='landing_dog_details.php?search=" . urlencode($dog['breed']) . "'\">Read More</button>";
                            echo "</div>"; 
                            echo "</div>"; 
                        }
                    }
                }

                //display user posts
                foreach ($posts as $postData) {
                    if ($postData && is_array($postData) && (empty($searchTag) || in_array($searchTag, array_map('strtolower', $postData['tags'])))) {
                        $commentCount = count($postData['comments']); // Get the number of comments
                        $shortText = strlen($postData['text']) > 100 ? substr($postData['text'], 0, 100) . '...' : $postData['text'];
                        echo "<div class='post-tile' data-post-id='" . htmlspecialchars($postData['id']) . "' data-post-text='" . htmlspecialchars($postData['text']) . "' data-post-tags='" . htmlspecialchars(implode(',', array_map('strtolower', $postData['tags']))) . "' onclick=\"openModal('" . htmlspecialchars($postData['image']) . "', '" . htmlspecialchars($postData['text']) . "', '" . htmlspecialchars($postData['id']) . "', '" . htmlspecialchars(json_encode($postData['comments'])) . "', '" . htmlspecialchars($postData['username']) . "', '" . htmlspecialchars(json_encode($postData['tags'])) . "')\">";
                        echo "<img src='" . htmlspecialchars($postData['image']) . "' alt='Post Image'>";
                        echo "<div class='post-details'>";
                        echo "<p class='text-preview'>" . htmlspecialchars(substr($postData['text'], 0, 100)) . (strlen($postData['text']) > 100 ? '...' : '') . "</p>";
                        echo "<p><strong>Post ID:</strong> " . htmlspecialchars($postData['id']) . "</p>";
                        echo "<p><strong>Posted by:</strong> " . htmlspecialchars($postData['username']) . "</p>";
                        echo "<p><strong>Tags:</strong> " . implode(', ', array_map('strtolower', $postData['tags'])) . "</p>";
                        echo "<p><strong>Comments:</strong> <span class='comment-count'>$commentCount</span></p>"; // Display the number of comments
                        echo "</div>"; 
                        echo "</div>"; 
                    }
                }

                echo "</div>"; 
            } else {
                echo "<p>No posts or dog entries available.</p>";
            }
            ?>
            </div>
        </div>
    </section>
        
    <script>
    function clearBreedSearch() {
        document.getElementById('search').value = '';
        window.location.href = 'landing.php';
    }

    function searchPosts() {
        const searchInput = document.getElementById('search').value.trim();
        if (searchInput) {
            window.location.href = `landing.php?search=${encodeURIComponent(searchInput)}`;
        } else {
            alert('Please enter a tag to search for.');
        }
    }

    function clearAdvancedSearch() {
        document.getElementById('advanced_search').value = '';
        filterPosts('');
    }

    function filterPosts(query) {
        const searchTag = document.getElementById('search').value.trim().toLowerCase();
        console.log('Search Tag:', searchTag); // Debugging
        console.log('Query:', query); // Debugging

        const tiles = document.getElementById('post-tiles').children;
        for (let tile of tiles) {
            const postText = tile.getAttribute('data-post-text') ? tile.getAttribute('data-post-text').toLowerCase() : '';
            const postTags = tile.getAttribute('data-post-tags') ? tile.getAttribute('data-post-tags').toLowerCase().split(',') : [];
            console.log('Post Tags:', postTags); // Debugging
            console.log('Post Text:', postText); // Debugging

            if ((postText.includes(query.toLowerCase())) && (searchTag === '' || postTags.includes(searchTag))) {
                tile.style.display = 'block';
            } else {
                tile.style.display = 'none';
            }
        }
    }

    document.getElementById('advanced_search').addEventListener('input', function(event) {
        filterPosts(event.target.value);
    });

    function openModal(imageSrc, text, postId, comments, username, tags) {
        const modal = document.getElementById('myModal');
        const modalImage = document.getElementById('modalImage');
        const modalText = document.getElementById('modalText');
        const modalUsername = document.getElementById('modalUsername');
        const modalTags = document.getElementById('modalTags');
        const commentsSection = document.getElementById('commentsSection');
        const commentPostId = document.getElementById('commentPostId');

        modalImage.src = imageSrc;
        modalText.textContent = text;
        modalUsername.textContent = `Posted by: ${username}`;
        modalTags.textContent = "Tags: " + JSON.parse(tags).join(', ');
        commentPostId.value = postId;

        commentsSection.innerHTML = ''; // Clear existing comments
        const commentsArray = JSON.parse(comments).reverse(); // Reverse the comments array
        commentsArray.forEach(function(comment) {
            const commentDiv = document.createElement('div');
            commentDiv.textContent = `${comment.username}: ${comment.text}`; // Username and comment text
            commentsSection.appendChild(commentDiv);
        });

        modal.style.display = "block";
    }

    function closeModal() {
        const modal = document.getElementById('myModal');
        modal.style.display = "none";
    }
    </script>
</body>
</html>
