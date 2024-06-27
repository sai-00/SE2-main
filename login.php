<!DOCTYPE html>
<html>
    <head>
        <title>Pawpedia - Login</title>
        <link rel="stylesheet" href="../css/site_layout.css">
        <link rel="stylesheet" href="../css/login.css">
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

                <div class="login-box">
                <form method="POST" action="login.php">
                    <h1>Login</h1>
                    <input type="text" placeholder="Username" name="username" id="username">
                    <input type="password" placeholder="Password" name="password" id="password">
                    <button onclick="login()">Login</button>
                    <hr>
                    <div class="text-for-redirect">
                        <p>not yet a member ?</p>
                        <a href="signup.php">sign up here</a>
                    </div>
                </form>
                </div>
            </section>
        
            <section class="right-sidebar">
                
            </section>
        </div>
        <?php
           session_start();

           if ($_SERVER['REQUEST_METHOD'] == 'POST') {
               $username = trim($_POST['username']);
               $password = $_POST['password'];
           
               if (empty($username) || empty($password)) {
                   echo "<script>alert('Username and password are required!');</script>";
               } else {
                   $file = 'users.json';
                   $users = json_decode(file_get_contents($file), true);
           
                   $validUser = false;
                   foreach ($users as $user) {
                       if ($user['username'] == $username && password_verify($password, $user['password'])) {
                           $validUser = true;
                           break;
                       }
                   }
           
                   if ($validUser) {
                       $_SESSION['username'] = $username;
                       header('Location: index.php');
                       exit;
                   } else {
                       echo "<script>alert('Invalid username or password!');</script>";
                   }
               }
           }       
        ?>
        <!-- <script type="module">
    // Import the functions you need from the Firebase Auth SDK
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

    // Function to handle login
    window.login = function() {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        signInWithEmailAndPassword(auth, email, password)
            .then((userCredential) => {
                // Signed in
                const user = userCredential.user;
                console.log('User signed in: ', user);
                alert('Login successful!');
                // Redirect to dashboard or another authenticated page
                window.location.href = 'index.php';
            })
            .catch((error) => {
                const errorCode = error.code;
                const errorMessage = error.message;
                console.error('Error: ', errorCode, errorMessage);
                alert(errorMessage);
            });
    };

    document.addEventListener('DOMContentLoaded', (event) => {
        // Check if Firebase is loaded
        console.log('Firebase is loaded successfully.');
    });
</script> -->

    </body>
</html>