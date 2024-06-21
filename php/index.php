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
                    <input type="text" id="search" placeholder="Search by tag">
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
                    <label for="tags">Tags (comma-separated):</label>
                    <input type="text" name="tags" id="tags" placeholder="e.g., dog, cute, animal" required>
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
                $searchTag = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
                
                if (file_exists($postsFile)) {
                    $posts = array_reverse(file($postsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
                    echo "<div class='tiles'>";
                    foreach ($posts as $post) {
                    $postData = json_decode($post, true);
                    if ($postData) {
                        if (empty($searchTag) || in_array($searchTag, $postData['tags'])) {
                            echo "<div class='post-tile' onclick=\"openModal('" . htmlspecialchars($postData['image']) . "', '" . htmlspecialchars($postData['text']) . "')\">";
                            echo "<img src='" . htmlspecialchars($postData['image']) . "' alt='Post Image'>";
                            echo "<div class='post-details'>";
                            echo "<p>" . htmlspecialchars($postData['text']) . "</p>";
                            echo "<p><strong>Post ID:</strong> " . htmlspecialchars($postData['id']) . "</p>";
                            echo "<p><strong>Tags:</strong> " . implode(', ', $postData['tags']) . "</p>";
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
                } else {
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
            const searchInput = document.getElementById('search').value;
            console.log('Search input:', searchInput); // Log search input
            if (searchInput) {
                fetchWikiEntry(searchInput);
                window.location.href = `index.php?search=${encodeURIComponent(searchInput)}`;
            } else {
                alert('Please enter a tag to search for.');
            }
        };
        
        async function fetchWikiEntry(term) {
            console.log('Fetching Wikipedia entry for:', term); // Log the search term
            try {
                const response = await fetch(`https://en.wikipedia.org/api/rest_v1/page/summary/${encodeURIComponent(term)}`);
                console.log('Response received:', response); // Log the response object
                if (!response.ok) {
                    console.error(`HTTP error! Status: ${response.status}`);
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                const data = await response.json();
                console.log('Data received:', data); // Log the response data
                displayWikiEntry(data);
            } catch (error) {
                console.error('Error fetching Wikipedia entry:', error);
            }
        }

        function displayWikiEntry(data) {
            console.log('Displaying Wikipedia entry:', data); // Log the data to be displayed
            const wikiEntryDiv = document.getElementById('wiki-entry');
            if (data.type === "standard") {
                wikiEntryDiv.innerHTML = `
                    <div class="wiki-entry-tile">
                        <h3>${data.title}</h3>
                        <img src="${data.thumbnail ? data.thumbnail.source : ''}" alt="${data.title} Thumbnail">
                        <p>${data.extract}</p>
                        <a href="${data.content_urls.desktop.page}" target="_blank">Read more on Wikipedia</a>
                    </div>
                `;
            } else {
                wikiEntryDiv.innerHTML = `<p>No Wikipedia entry found for "${data.title}".</p>`;
            }
        }

        window.clearSearch = function() {
            window.location.href = 'index.php';
        };
</script>
</body>
</html>
