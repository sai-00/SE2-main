<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Home</title>
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
            <div id="user-list">
                <!-- User list will be rendered here dynamically -->
            </div>
        </section>
    
        <section class="right-sidebar">
            
        </section>
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

        // Function to fetch users from Firestore
        const fetchUsers = async () => {
            try {
                const usersRef = collection(db, 'users');
                const querySnapshot = await getDocs(usersRef);

                querySnapshot.forEach((doc) => {
                    const userData = doc.data();
                    renderUser(userData);
                });
            } catch (error) {
                console.error('Error fetching users:', error);
                alert('Failed to fetch users. Please try again later.');
            }
        };

        // Function to render user data on the page
        const renderUser = (userData) => {
            const userListDiv = document.getElementById('user-list');
            const userCard = document.createElement('div');
            userCard.classList.add('user-card');
            userCard.innerHTML = `
                <h3>${userData.username}</h3>
                <p>Email: ${userData.email}</p>
            `;
            userListDiv.appendChild(userCard);
        };

        // Check authentication state on page load
        checkAuthState();
</script>
</body>
</html>
