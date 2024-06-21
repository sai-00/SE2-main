<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Home</title>
    <link rel="stylesheet" href="../css/site_layout.css">
    <link rel="stylesheet" href="../css/users.css">
    <script src="../js/nav.js"></script>
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

    <div id="user-list" class="user-list">
        <script src="../js/users.js"></script>
    </div>

    
    <script type="module">
        // Import the functions you need from the Firebase Auth SDK
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
        import { getAuth, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";
        import { getFirestore, collection, getDocs } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";

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
        const db = getFirestore(app);

        // Function to check authentication state
        const checkAuthState = () => {
            onAuthStateChanged(auth, async (user) => {
                if (user) {
                    // User is signed in
                    console.log('User is signed in:', user);

                    // Fetch users from Firestore and render them
                    await fetchUsers();
                } else {
                    // User is not signed in
                    console.log('User is not signed in.');
                    // Redirect to login page
                    window.location.href = 'login.php';
                }
            });
        };

        checkAuthState();
    </script>
</body>
</html>
