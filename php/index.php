<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

// Define file paths
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

        // Return the new comment and comment count as JSON (for AJAX response)
        echo json_encode(['comment' => $comment, 'commentCount' => count($post['comments'])]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Home</title>
    <link rel="stylesheet" href="../css/site_layout.css">
    <link rel="stylesheet" href="../css/modal.css">
    <script src="../js/modal.js"></script>
    <style>
        .modal a {
            color: inherit; 
            text-decoration: underline;
            cursor: pointer;
        }

        .modal a:hover {
            color: #7ca4e6;
            transition: 0.3s;
        }

        #advanced_search 
        {
            padding: 10px;
            border-radius: 20px;
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
            <a href="index.php"><strong>Pawpedia</strong></a>
        </div>
        
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="users.php">Users</a>
            <a href="profile.php">Profile</a>
            <a href="#" id="logout">Logout</a>
        </div>
    </nav>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <center><img id="modalImage" src="" alt="Modal Image">
            <p id="modalText"></p></center>
            <p id="modalUsername"></p>
            <p id="modalTags"></p>
            <form id="commentForm" method="post" action="index.php" onsubmit="submitComment(event)">
                <input type="hidden" name="comment_post_id" id="commentPostId">
                <div class="form-parent">
                    <div class="form-text">
                        <textarea name="comment_text" id="commentText" rows="2" cols="50" placeholder="Add a comment" required></textarea>
                    </div>
                    <div class="form-button">
                        <button type="submit">Comment</button>
                    </div>
                </div>
            </form>
            <br><hr>
            <h3>Comments</h3>
            <div id="commentsSection"></div>
        </div>
    </div>

    <section class="main-content">
        <div class="posting-form">
            <div class="search-bar">
                <div class="search-container"> <!--new div------------------------------ -->
                <br>
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
                <button onclick="clearBreedSearch()">Clear Breed Search</button>
                <br><br></div><!--new------------------------------ -->
            </div>
            <div class="search-container"> <!--new div------------------------------ -->
            <br>
            <label for="advanced_search">Advanced Search:</label> <br>
            <input type="text" id="advanced_search" placeholder="Search keywords (i.e. loud)">
            <button onclick="clearAdvancedSearch()">Clear Advanced Search</button>
            <br><br></div><!--new------------------------------ -->
            <br>
            <div class="search-container"> <!--new div------------------------------ -->
            <br>
            <h2><u>Make a post!</u></h2>
            <form action="index.php" method="post" enctype="multipart/form-data">
                <textarea name="text" id="text" rows="4" cols="50" placeholder="Enter your text" required></textarea>
                <br>
                <label for="image">Choose an image:</label>
                <input type="file" name="image" id="image" required>
                <br><br><br>
                <label for="tags">Select breed:</label>
                <select id="tags" name="tags" required>
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
                <br><br>
                <button type="submit">Post</button>
                </div>
            </form>
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
                            echo "<button onclick=\"window.location.href='dog_details.php?search=" . urlencode($dog['breed']) . "'\">Read More</button>";
                            echo "</div>";
                            echo "</div>";
                        }
                    }
                }

                // Display user posts
                foreach ($posts as $postData) {
                    if ($postData && is_array($postData) && (empty($searchTag) || in_array($searchTag, array_map('strtolower', $postData['tags'])))) {
                        $commentCount = count($postData['comments']); // Get the number of comments
                        echo "<div class='post-tile' data-post-id='" . htmlspecialchars($postData['id']) . "' data-post-text='" . htmlspecialchars($postData['text']) . "' data-post-tags='" . htmlspecialchars(implode(',', array_map('strtolower', $postData['tags']))) . "' onclick=\"openModal('" . htmlspecialchars($postData['image']) . "', '" . htmlspecialchars($postData['text']) . "', '" . htmlspecialchars($postData['id']) . "', '" . htmlspecialchars(json_encode($postData['comments'])) . "', '" . htmlspecialchars($postData['username']) . "', '" . htmlspecialchars(json_encode($postData['tags'])) . "')\">";
                        echo "<img src='" . htmlspecialchars($postData['image']) . "' alt='Post Image'>";
                        echo "<div class='post-details'>";
                        echo "<p class='text-preview'>" . htmlspecialchars(substr($postData['text'], 0, 100)) . (strlen($postData['text']) > 100 ? '...' : '') . "</p>";
                        echo "<p><strong>Posted by:</strong> " . htmlspecialchars($postData['username']) . "</p>";
                        echo "<p><strong>Tags:</strong> " . implode(', ', array_map('strtolower', $postData['tags'])) . "</p>";
                        echo "<p><strong>Comments:</strong> <span class='comment-count'>$commentCount</span></p>"; // Display the number of comments
                        echo "<button onclick=\"openModal('" . htmlspecialchars($postData['image']) . "', '" . htmlspecialchars($postData['text']) . "', '" . htmlspecialchars($postData['id']) . "', '" . htmlspecialchars(json_encode($postData['comments'])) . "', '" . htmlspecialchars($postData['username']) . "', '" . htmlspecialchars(json_encode($postData['tags'])) . "'); return false;\">Comment</button>";
                        echo "</div>";
                        echo "</div>";

                    }
                }
            } else {
                echo "<p>No posts or dog entries available.</p>";
            }
            ?>
            </div>
        </div>
    </section>

    <script>
    function logout() {
        window.location.href = "login.php";
        sessionStorage.clear();
    }

    document.addEventListener('DOMContentLoaded', function() {
        var logoutLink = document.getElementById('logout');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default link behavior
                logout(); // Call logout function
            });
        } else {
            console.error('Logout link element not found.');
        }
    });

    function clearBreedSearch() {
        document.getElementById('search').value = '';
        window.location.href = 'index.php';
    }

    function searchPosts() {
        const searchInput = document.getElementById('search').value.trim();
        if (searchInput) {
            window.location.href = `index.php?search=${encodeURIComponent(searchInput)}`;
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

    window.submitComment = function(event) {
        event.preventDefault(); // Prevent form from submitting normally
        const postId = document.getElementById('commentPostId').value;
        const commentText = document.getElementById('commentText').value;

        if (commentText.trim() === '') {
            alert('Comment cannot be empty');
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'index.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                const newComment = response.comment;
                const commentCount = response.commentCount;

                // Add new comment to the modal
                const commentDiv = document.createElement('div');
                commentDiv.innerHTML = `<strong><a href="userpage.php?id=${newComment.username}">${newComment.username}</a></strong>: ${newComment.text}`;
                commentsSection.insertBefore(commentDiv, commentsSection.firstChild); // Insert the new comment at the top
                document.getElementById('commentText').value = ''; // Clear the textarea

                // Update the comment count on the post card
                const postTile = document.querySelector(`.post-tile[data-post-id='${postId}']`);
                const commentCountElement = postTile.querySelector('.comment-count');
                commentCountElement.textContent = commentCount;
            }
        };
        xhr.send('comment_post_id=' + encodeURIComponent(postId) + '&comment_text=' + encodeURIComponent(commentText));
    };
    </script>
</body>
</html>
