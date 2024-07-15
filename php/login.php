@ -0,0 +1,129 @@
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
                        <p>Not yet a member ?</p>
                        <a href="signup.php">sign up here</a>
                    </div>
                </form>
                </div>
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
                   $file = '../json/users.json';
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
    </body>
</html>