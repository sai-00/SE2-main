<!DOCTYPE html>
<html lang="en">
<?php
include ("php/functions.php");
?>
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
            <a href="index.html">Home</a>
            <a href="#about">Sec1</a>
            <a href="#contact">Sec2</a>
        </div>
    </nav>

    <div class="content-container">
        <section class="left-sidebar">
            
        </section>
    
        <section class="main-content">
            <div id="inset_post" class="">
                <center>
                <form action="home.php?id=?php eho $userID ?>" method="post" id="f" enctype="multipart/form-data">
                <textarea class="form-control" id="content" rows="4" name="content" placeholder="insert textl"></textarea><br>
                <label class="btn btn-warning" id="upload_image_button"> Select Image
                <input type="file" name="upload_image" size="30">
                </label>
                <button id="btn--pos" class="btn btn-success" name="sub"> POST</button>
                </form>
                <?php insertPostt(); ?>
                </center>
            </div>
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
                // Optionally redirect to dashboard or another authenticated page
                window.location.href = 'index.php';
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
</script>
</body>
</html>
