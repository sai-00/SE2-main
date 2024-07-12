<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Home</title>
    <link rel="stylesheet" href="../css/site_layout.css">
    <link rel="stylesheet" href="../css/modal.css">
    <script src="../js/modal.js"></script>
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

    <section class="main-content">

        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <img id="modalImage" src="" alt="Modal Image">
                <p id="modalText"></p>
                <p id="modalUsername"></p>
                <p id="modalTags"></p>
                <form id="commentForm" method="post" action="index.php">
                    <input type="hidden" name="comment_post_id" id="commentPostId">
                    <textarea name="comment_text" id="commentText" rows="2" cols="50" placeholder="Add a comment" required></textarea>
                    <br>
                    <button type="submit">Comment</button>
                </form>
                <br><hr>
                <h3>Comments</h3>
                <div id="commentsSection"></div>
            </div>
        </div>

        <div class="posting-form">
            <div class="search-bar">
                <label for="search">Search a breed:</label>
                <select id="search" name="search">
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
                <button onclick="searchPosts()">Search</button>
                <button onclick="clearSearch()">Clear Search</button>
            </div>
            <br><br>
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
            </form>
            <br><br>
            <div id="wiki-entry" class="wiki-entry"></div>
        </div>

        <div class="posts">
            <?php
            if (!isset($_SESSION['username'])) {
                header('Location: login.php');
                exit;
            }

            $username = $_SESSION['username'];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['text']) && isset($_FILES['image'])) {
                    $uploadDir = 'uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
                    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

                    // Check if image file is an actual image or fake image
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

                    // Check if $uploadFile is set to write
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        die("Sorry, there was an error uploading your file.");
                    }

                    // Save the text and tags
                    $text = htmlspecialchars($_POST['text']);
                    $tags = htmlspecialchars($_POST['tags']);
                    $tagArray = array_map('trim', explode(',', $tags));
                    $tagArray = array_map('strtolower', $tagArray); // Convert tags to lowercase

                    $postData = [
                        'id' => uniqid(),
                        'username' => $username,
                        'image' => $uploadFile,
                        'text' => $text,
                        'tags' => $tagArray,
                        'comments' => []
                    ];

                    file_put_contents('../json/posts.json', json_encode($postData) . PHP_EOL, FILE_APPEND);
                } elseif (isset($_POST['comment_post_id']) && isset($_POST['comment_text'])) {
                    $commentPostId = htmlspecialchars($_POST['comment_post_id']);
                    $commentText = htmlspecialchars($_POST['comment_text']);
            
                    $postsFile = '../json/posts.json';
                    if (file_exists($postsFile)) {
                        $posts = file($postsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                        $updatedPosts = [];
                        foreach ($posts as $post) {
                            $postData = json_decode($post, true);
                            if ($postData && $postData['id'] === $commentPostId) {
                                $comment = [
                                    'username' => $username,
                                    'text' => $commentText
                                ];
                                $postData['comments'][] = $comment;
                            }
                            $updatedPosts[] = json_encode($postData);
                        }
                        file_put_contents($postsFile, implode(PHP_EOL, $updatedPosts) . PHP_EOL);
                    }
                }
            }

            $postsFile = '../json/posts.json';
            $dogsFile = '../json/dogs.json';

            if (file_exists($postsFile) || file_exists($dogsFile)) {
                $posts = file_exists($postsFile) ? array_reverse(file($postsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) : [];
                $dogs = file_exists($dogsFile) ? file($dogsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
                
                $searchTag = isset($_GET['search']) ? strtolower(htmlspecialchars($_GET['search'])) : '';

                echo "<div class='tiles'>";
                
                //display wiki entry
                if (!empty($searchTag)) {
                    foreach ($dogs as $dog) {
                        $dogData = json_decode($dog, true);
                        if ($dogData && is_array($dogData) && strtolower($dogData['breed']) === strtolower($searchTag)) {
                            echo "<div class='post-tile'" . htmlspecialchars($dogData['image']) . "', '')\" style='background-color: #dba181;'>";
                            echo "<img src='" . htmlspecialchars($dogData['image']) . "' alt='Dog Image'>";
                            echo "<div class='post-details'>";
                            echo "<p><strong><u>Wiki Entry!</u></strong></p>";
                            echo "<p><strong>Breed:</strong> " . htmlspecialchars($dogData['breed']) . "</p>";
                            echo "<br><br>";
                            echo "<p>Click 'read more' to redirect to " . htmlspecialchars($dogData['breed']) . " information.</p>";
                            echo "<button onclick=\"window.location.href='dog_details.php?search=" . urlencode($dogData['breed']) . "'\">Read More</button>";
                            echo "</div>"; 
                            echo "</div>"; 
                        }
                    }
                }
                
                //display user posts
                foreach ($posts as $post) {
                    $postData = json_decode($post, true);
                    if ($postData && is_array($postData) && (empty($searchTag) || in_array($searchTag, array_map('strtolower', $postData['tags'])))) {
                        echo "<div class='post-tile' onclick=\"openModal('" . htmlspecialchars($postData['image']) . "', '" . htmlspecialchars($postData['text']) . "', '" . htmlspecialchars($postData['id']) . "', '" . htmlspecialchars(json_encode($postData['comments'])) . "', '" . htmlspecialchars($postData['username']) . "', '" . htmlspecialchars(json_encode($postData['tags'])) . "')\">";
                        echo "<img src='" . htmlspecialchars($postData['image']) . "' alt='Post Image'>";
                        echo "<div class='post-details'>";
                        echo "<p>" . htmlspecialchars($postData['text']) . "</p>";
                        echo "<p><strong>Post ID:</strong> " . htmlspecialchars($postData['id']) . "</p>";
                        echo "<p><strong>Posted by:</strong> " . htmlspecialchars($postData['username']) . "</p>";
                        echo "<p><strong>Tags:</strong> " . implode(', ', array_map('strtolower', $postData['tags'])) . "</p>";
                        echo "<input type='hidden' name='post_id' value='" . htmlspecialchars($postData['id']) . "'>";
                        echo "<button onclick=\"openModal('" . htmlspecialchars($postData['image']) . "', '" . htmlspecialchars($postData['text']) . "', '" . htmlspecialchars($postData['id']) . "', '" . htmlspecialchars(json_encode($postData['comments'])) . "', '" . htmlspecialchars($postData['username']) . "', '" . htmlspecialchars(json_encode($postData['tags'])) . "'); return false;\">Comment</button>";
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

    function clearSearch() {
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

    function displayComments(comments) {
        const commentsSection = document.getElementById('commentsSection');
        commentsSection.innerHTML = ''; // Clear previous comments
        const parsedComments = JSON.parse(comments);
        parsedComments.forEach(comment => {
            const commentDiv = document.createElement('div');
            commentDiv.className = 'comment';
            const commentText = document.createElement('p');
            commentText.innerText = comment.text;
            const commentUser = document.createElement('p');
            commentUser.innerText = 'Comment by: ' + comment.username;
            commentDiv.appendChild(commentText);
            commentDiv.appendChild(commentUser);
            commentsSection.appendChild(commentDiv);
        });
    }

    document.getElementById('commentsSection').addEventListener('submit', function(event) {
        event.preventDefault();
        const commentText = document.getElementById('commentText').value.trim();
        const commentPostId = document.getElementById('commentPostId').value;
        if (commentText) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'index.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const newComment = {
                        username: '<?php echo $username; ?>',
                        text: commentText
                    };
                    const commentsArray = JSON.parse(xhr.responseText);
                    commentsArray.push(newComment);
                    displayComments(JSON.stringify(commentsArray));
                    document.getElementById('commentText').value = ''; 
                }
            };
            xhr.send('comment_post_id=' + encodeURIComponent(commentPostId) + '&comment_text=' + encodeURIComponent(commentText));
        }
    });
    </script>
</body>
</html>
