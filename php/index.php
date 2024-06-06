<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" ></script>
    <script src="https://ajax.googleapis.com/ajax/libs/cesiumjs/1.78/Build/Cesium/Cesium.js"></script>
    <link rel="stylesheet" href="../css/site_layout.css">
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

    <div class="content-container">
        <section class="left-sidebar">
            
        </section>
    
        <section class="main-content">
            <div class = "posting form">
            <form action="index.php" method="post" enctype="multipart/form-data">
                <label for="image">Choose an image:</label>
                <input type="file" name="image" id="image" required>
                <br><br>
                <label for="text">Enter your text:</label>
                <textarea name="text" id="text" rows="4" cols="50" required></textarea>
                <br><br>
                <button type="submit">Post</button>
            </form>
            </div>
        

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

            // Save the text
            $text = htmlspecialchars($_POST['text']);

            // Save the post data (e.g., to a database or file)
            // For simplicity, saving to a file
            $postData = [
            'image' => $uploadFile,
            'text' => $text,
            ];

            file_put_contents('posts.json', json_encode($postData) . PHP_EOL, FILE_APPEND);
            }

            // Display the uploaded posts
            $postsFile = 'posts.json';

            if (file_exists($postsFile)) {
            $posts = file($postsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            echo "<h2>Posts</h2>";
            foreach ($posts as $post) {
            $postData = json_decode($post, true);
            if ($postData) {
            echo "<div>
                ";
                echo "<img src='" . htmlspecialchars($postData['image']) . "' alt='Post Image' style='width:300px;height:auto;'><br>" ;
                           echo "<p>" . htmlspecialchars($postData['text']) . "</p>" ;
                           echo "</div><br>" ;
                           }
                           }
                           } else {
                           echo "<h2>No posts available.</h2>" ;
                           }
                           ?>
        </section>
    
        <section class="right-sidebar">
            
        </section>
    </div>
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

    document.addEventListener('DOMContentLoaded', (event) => {
        // Check if Firebase is loaded
        console.log('Firebase is loaded successfully.');

        // Check authentication state on page load
        checkAuthState();
    });

    const logout = () => {
        firebase.auth().signOut().then(() => {
            // Sign-out successful.
            console.log('User signed out.');
            // Redirect to login page or any other page
            window.location.href = '/login.php';
        }).catch((error) => {
            // An error happened.
            console.error('Sign out error:', error);
        });
    };

    document.addEventListener('DOMContentLoaded', (event) => {
        // Check if Firebase is loaded
        console.log('Firebase is loaded successfully.');

        // Logout link event listener
        const logoutLink = document.getElementById('logout');
        if (logoutLink) {
            logoutLink.addEventListener('click', (event) => {
                event.preventDefault(); // Prevent default link behavior
                logout(); // Call logout function
            });
        } else {
            console.error('Logout link element not found.');
        }
    });
</script>
</body>
</html>
