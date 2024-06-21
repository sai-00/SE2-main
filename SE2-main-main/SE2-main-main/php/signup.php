<!DOCTYPE html>
<html>
    <head>
        <title>Pawpedia - Signup</title>
        <link rel="stylesheet" href="../css/site_layout.css">
        <link rel="stylesheet" href="../css/signup.css">
    </head>
    <body>
        <nav class="top-navbar">
            <div class="logo">
                <strong>Pawpedia</strong> 
            </div>
            
        </nav>
    
        <div class="content-container">
            <section class="left-sidebar">
                
            </section>
        
            <section class="main-content">
                <div class = "header">
                    <strong>pawpedia</strong>
                </div>

                <div class="signup-box">
                    <h1>Register</h1>
                    <input type="text" placeholder="Username" id="username">
                    <input type="text" placeholder="Email" id="email">
                    <input type="password" placeholder="Password" id="password">
                    <button onclick="signup()">Register</button>
                    <hr>
                    <div class="text-for-redirect">
                        <p>already a member ?</p>
                        <a href="login.html">login here</a>
                    </div>
                </div>
            </section>
        
            <section class="right-sidebar">
                
            </section>
        </div>
        <script type="module">
        // Import the functions you need from the SDKs you need
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
        import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-analytics.js";
        import { getAuth, createUserWithEmailAndPassword, signInWithEmailAndPassword, signOut } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

        // Your web app's Firebase configuration
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
        const analytics = getAnalytics(app);
        const auth = getAuth(app);

        document.addEventListener('DOMContentLoaded', (event) => {
            // Check if Firebase is loaded
            console.log('Firebase is loaded successfully.');

            // Sign Up Function
            window.signup = function() {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;

                createUserWithEmailAndPassword(auth, email, password)
                    .then((userCredential) => {
                        // Signed up
                        const user = userCredential.user;
                        console.log('User signed up: ', user);
                        alert('User signed up successfully!');
                        window.location.href = 'login.php';
                    })
                    .catch((error) => {
                        const errorCode = error.code;
                        const errorMessage = error.message;
                        console.error('Error: ', errorCode, errorMessage);
                        alert(errorMessage);
                    });
            };
        });
        
    </script>
    </body>
</html>