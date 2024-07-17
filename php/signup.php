@ -0,0 +1,128 @@
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
            
        
            <section class="main-content">
                <div class = "header">
                    <strong>pawpedia</strong>
                </div>

                <div class="signup-box">
                <form method="POST" action="signup.php" onsubmit="return validateForm()">
                    <h1>Register</h1>
                    <input type="text" placeholder="Username" name="Username" id="Username">
                    <input type="password" placeholder="Password" name="Password" id="Password">
                    <input type="password" placeholder="Confirm Password" name="confirmPassword" id="confirmPassword">
                    <button type="submit">Register</button>
                    <hr>
                    <div class="text-for-redirect">
                        <p>Already a member ?</p>
                        <a href="login.php">login here</a> <br><br>
                    </div>
                </form>
                <br><br>
                <a href="landing.php">Proceed without an account ? -></a>
                </div>
            </section>
        </div>
        <?php
           if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['Username']);
            $password = $_POST['Password'];
        
            if (empty($username) || empty($password)) {
                echo "<script>alert('Username and password are required!');</script>";
            } else {
                $file = '../json/users.json';
                $users = json_decode(file_get_contents($file), true);
        
                $userExists = false;
                foreach ($users as $user) {
                    if ($user['username'] == $username) {
                        $userExists = true;
                        break;
                    }
                }
        
                if ($userExists) {
                    echo "<script>alert('Username already exists!');</script>";
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $users[] = ['username' => $username, 'password' => $hashedPassword];
                    file_put_contents($file, json_encode($users));
                    
                    header('Location: login.php');
                    exit; 
                }
            }
        }
        ?>
        <script>
        function validateForm() 
        {
            var password = document.getElementById('Password').value;
            var confirmPassword = document.getElementById('confirmPassword').value;
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return false;
            }
            return true;
        }
    </script>
    </body>
</html>