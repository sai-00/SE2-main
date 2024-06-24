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
            width: 75%;
            height: 50%;
        }

        .modal-content {
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 700px;
            background-color: #fefefe;
            position: relative;
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
                    <label for="search">Select a breed:</label>
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
                        <option value="Toy Poodle">Toy Poodle</option>
                        <option value="French Bulldog">French Bulldog</option>
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

                    file_put_contents('posts.json', json_encode($postData) . PHP_EOL, FILE_APPEND);

                    
                }

                // Display the uploaded posts
                $postsFile = 'posts.json';
                $searchTag = isset($_GET['search']) ? strtolower(htmlspecialchars($_GET['search'])) : '';

                if (file_exists($postsFile)) {
                $posts = array_reverse(file($postsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
                echo "<div class='tiles'>";
                foreach ($posts as $post) {
                $postData = json_decode($post, true);
                if ($postData) {
            // Check if searchTag is empty or matches any tag in $postData['tags']
                if (empty($searchTag) || in_array($searchTag, array_map('strtolower', $postData['tags']))) {
                        echo "<div class='post-tile' onclick=\"openModal('" . htmlspecialchars($postData['image']) . "', '" . htmlspecialchars($postData    ['text']) . "')\">";
                        echo "<img src='" . htmlspecialchars($postData['image']) . "' alt='Post Image'>";
                        echo "<div class='post-details'>";
                        echo "<p>" . htmlspecialchars($postData['text']) . "</p>";
                        echo "<p><strong>Post ID:</strong> " . htmlspecialchars($postData['id']) . "</p>";
                        echo "<p><strong>Tags:</strong> " . implode(', ', array_map('strtolower', $postData['tags'])) . "</p>";
                        echo "<form method='post' action='index.php'>";
                        echo "<input type='hidden' name='post_id' value='" . htmlspecialchars($postData['id']) . "'>";
                        echo "<button type='submit' name='like' value='" . htmlspecialchars($postData['id']) . "'>Like</button>";
                        echo "<span>Likes: " . htmlspecialchars($postData['likes']) . "</span>";
                        echo "</form>";
                        echo "</div>"; // Close post-details
                        echo "</div>"; // Close post-tile
                        }
                    }
                }
                    echo "</div>"; // Close tiles
                }else {
                    echo "<div class='post-tile'>";
                    echo "<p><strong>Post ID:</strong> " . htmlspecialchars($postData['id']) . "</p>";
                    echo "<p>Image not available</p>"; // Provide alternative content
                    echo "</div>"; // Close post-tile
                }
                ?>
            </div>
        </section>
        
    <script type="module">
    // Import the functions you need from the Firebase Auth SDK
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
    import { getAuth, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

    // Your Firebase configuration
    const firebaseConfig = {
        apiKey: "AIzaSyBVPfjrG7iszVvFbTMigd2_mDM_ViY-XOQ",
        authDomain: "pawpedia-d4569.firebaseapp.com",
        databaseURL: "https://pawpedia-d4569-default-rtdb.firebaseio.com",
        projectId: "pawpedia-d4569",
        storageBucket: "pawpedia-d4569.appspot.com",
        messagingSenderId: "792561362206",
        appId: "1:792561362206:web:457b81696cb11c4445550f",
        measurementId: "G-FEB9E1434F"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);

    // Function to check authentication status
    const checkAuthState = () => {
        onAuthStateChanged(auth, (user) => {
            if (user) {
                // User is signed in
                console.log('User is signed in:', user);
            } else {
                // User is not signed in
                console.log('User is not signed in.');
                alert('Please sign in before using the site')
                // Redirect to login page
                window.location.href = 'login.php';
            }
        });
    };

    if (typeof firebase === 'undefined') {
        console.error('Firebase SDK not loaded');
    } else {
        console.log('Firebase SDK loaded');
    }

    document.addEventListener('DOMContentLoaded', (event) => {
        // Check if Firebase is loaded
        console.log('Firebase is loaded successfully.');

        // Check authentication state on page load
        checkAuthState();
    });

    function logout() {
    
    window.location.href="login.php"
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
