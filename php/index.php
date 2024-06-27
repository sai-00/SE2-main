<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Home</title>
    
    <script src="https://ajax.googleapis.com/ajax/libs/cesiumjs/1.78/Build/Cesium/Cesium.js"></script>
    <link rel="stylesheet" href="../css/site_layout.css">
    <script src="../js/modal.js"></script>

    <style>
        /* CSS for modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }

        .modal img 
        {
            width: 60%;
            height: auto;
        }

        .modal-content {
            margin: 10% auto;
            padding: 20px;
            width: 100%;
            max-width:50%;
            background-color: #fefefe;
            position: relative;
            background-color: #dba181;
            border-radius: 20px;
        }

        .close {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 25px;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
        }

        .post {
            margin: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            width: 300px;
            cursor: pointer;
        }

        .post img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-bottom: 10px;
        }
    </style>

</head>
<body>
    <nav class="top-navbar">
        <div class="logo">
            <strong>Pawpedia</strong> 
        </div>
        
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="users.php">Users</a>
            <a href="profile.php">Profile</a>
            <a href="#" id="logout">Logout</a>
        </div>
    </nav>

    <section class="main-content">

        <!-- Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <img id="modalImage" src="" alt="Modal Image">
                <p id="modalText"></p>
            </div>
        </div>

            <div class = "posting-form">
                <div class="search-bar">
                    <label for="search">Search a breed:</label>
                    <input type="text" id="search" name="search" placeholder="Search for breed">
                    <br>
                    <button onclick="searchPosts()">Search</button>
                    <button onclick="clearSearch()">Clear Search</button>
                </div>
                <br><br>
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
                        <option value="Poodle">Toy Poodle</option>
                        <option value="Bulldog">French Bulldog</option>
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
                <div id="wiki-entry" class="wiki-entry">

                </div>
            </div>

            <div class="posts">

            <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

                    // Save the post data (e.g., to a database or file)
                    // For simplicity, saving to a file
                    $postData = [
                        'id' => uniqid(),
                        'image' => $uploadFile,
                        'text' => $text,
                        'tags' => $tagArray,
                        'likes' => 0
                    ];

                    file_put_contents('../json/posts.json', json_encode($postData) . PHP_EOL, FILE_APPEND);

                    
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
                            echo "<div class='post-tile' onclick=\"openModal('" . htmlspecialchars($postData['image']) . "', '" . htmlspecialchars($postData['text']) . "')\">";
                            echo "<img src='" . htmlspecialchars($postData['image']) . "' alt='Post Image'>";
                            echo "<div class='post-details'>";
                            echo "<p>" . htmlspecialchars($postData['text']) . "</p>";
                            echo "<p><strong>Post ID:</strong> " . htmlspecialchars($postData['id']) . "</p>";
                            echo "<p><strong>Tags:</strong> " . implode(', ', array_map('strtolower', $postData['tags'])) . "</p>";
                            echo "<form method='post' action='index.php'>";
                            echo "<input type='hidden' name='post_id' value='" . htmlspecialchars($postData['id']) . "'>";
                            // echo "<button onclick=\"likePost('" . htmlspecialchars($postData['id']) . "')\">Like</button>";
                            // echo "<span id='likes_" . htmlspecialchars($postData['id']) . "'>Likes: " . htmlspecialchars($postData['likes']) . "</span>";
                            echo "</form>";
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
        
    <script type="module">
    function logout() {
    
    window.location.href="login.php"
    sessionStorage.clear()
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

     window.clearSearch = function() {
         window.location.href = 'index.php';
     };

    window.searchPosts = function() {
             const searchInput = document.getElementById('search').value.trim();
             console.log('Search input:', searchInput); // Log search input
             if (searchInput) {
                 window.location.href = `index.php?search=${encodeURIComponent(searchInput)}`;
             } else {
                 alert('Please enter a tag to search for.');
             }
         };
        
</script>
</body>
</html>
