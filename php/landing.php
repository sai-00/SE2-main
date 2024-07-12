<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Home</title>
    
    <script src="https://ajax.googleapis.com/ajax/libs/cesiumjs/1.78/Build/Cesium/Cesium.js"></script>
    <link rel="stylesheet" href="../css/site_layout.css">
    <link rel="stylesheet" href="../css/modal.css">
    <script src="../js/landing_modal.js"></script>
    <style>
        .search-form
        {
            background-color: #dba181;
            width: 45%;
            max-width: 15%;
            padding-top: 20px;
            padding-left: 20px;
            padding-right: 40px;
        }    

        .search-form button
        {
            padding: 10px;
            text-align: center;
            width: 35%;
        }
    </style>
</head>
<body>
    <nav class="top-navbar">
        <div class="logo">
            <strong>Pawpedia</strong> 
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
            </div>
        </div>

        <div class="search-form">
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

                $postData = [
                    'id' => uniqid(),
                    'username' => $username,
                    'image' => $uploadFile,
                    'text' => $text,
                    'tags' => $tagArray,
                    'comments' => []
                ];

                file_put_contents('../json/posts.json', json_encode($postData) . PHP_EOL, FILE_APPEND);
            }
            
            if (isset($_POST['comment_post_id']) && isset($_POST['comment_text'])) {
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
                            echo "<button onclick=\"window.location.href='landing_dog_details.php?search=" . urlencode($dogData['breed']) . "'\">Read More</button>";
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
    window.clearSearch = function() {
        window.location.href = 'landing.php';
    };

    window.searchPosts = function() {
        const searchInput = document.getElementById('search').value.trim();
        console.log('Search input:', searchInput);
        if (searchInput) {
            window.location.href = `landing.php?search=${encodeURIComponent(searchInput)}`;
        } else {
            alert('Please enter a tag to search for.');
        }
    };
</script>
</body>
</html>