<?php
session_start();

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
                <form id="commentForm" method="post" action="index.php" onsubmit="submitComment(event)">
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

            if (file_exists($postsFile) || file_exists($dogsFile)) {
                $posts = file_exists($postsFile) ? array_reverse(json_decode(file_get_contents($postsFile), true)) : [];
                $dogs = file_exists($dogsFile) ? json_decode(file_get_contents($dogsFile), true) : [];
                
                $searchTag = isset($_GET['search']) ? strtolower(htmlspecialchars($_GET['search'])) : '';

                echo "<div class='tiles'>";
                
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
                        echo "<div class='post-tile' onclick=\"openModal('" . htmlspecialchars($postData['image']) . "', '" . htmlspecialchars($postData['text']) . "', '" . htmlspecialchars($postData['id']) . "', '" . htmlspecialchars(json_encode($postData['comments'])) . "', '" . htmlspecialchars($postData['username']) . "', '" . htmlspecialchars(json_encode($postData['tags'])) . "')\">";
                        echo "<img src='" . htmlspecialchars($postData['image']) . "' alt='Post Image'>";
                        echo "<div class='post-details'>";
                        echo "<p>" . htmlspecialchars($postData['text']) . "</p>";
                        echo "<p><strong>Post ID:</strong> " . htmlspecialchars($postData['id']) . "</p>";
                        echo "<p><strong>Posted by:</strong> " . htmlspecialchars($postData['username']) . "</p>";
                        echo "<p><strong>Tags:</strong> " . implode(', ', array_map('strtolower', $postData['tags'])) . "</p>";
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

    window.submitComment = function(event) {
        event.preventDefault(); // Prevent form from submitting normally
        const postId = document.getElementById('commentPostId').value;
        const commentText = document.getElementById('commentText').value;

        if (commentText.trim() === '') {
            alert('Comment cannot be empty');
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_comment.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const newComment = JSON.parse(xhr.responseText);
                const commentDiv = document.createElement('div');
                commentDiv.textContent = newComment.username + ": " + newComment.text;
                document.getElementById('commentsSection').appendChild(commentDiv);
                document.getElementById('commentText').value = ''; // Clear the textarea
            }
        };
        xhr.send('comment_post_id=' + encodeURIComponent(postId) + '&comment_text=' + encodeURIComponent(commentText));
    };
    </script>
</body>
</html>
