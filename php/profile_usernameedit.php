<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Profile</title>
    <link rel="stylesheet" href="../css/site_layout.css">
    <link rel="stylesheet" href="../css/profile.css">
    <script src="../js/nav.js"></script>
    <style>
        .logo a {
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="top-navbar">
        <div class="logo">
            <a href="index.php"><strong>Pawpedia</strong></a>
        </div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="users.php">Users</a>
            <a href="profile.php">Profile</a>
            <a href="#" id="logout">Logout</a>
        </div>
    </nav>

    <?php
    session_start();

    // Redirect to login page if user is not logged in
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit;
    }

    // Retrieve username from session
    $username = $_SESSION['username'];

    // Handle profile update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newUsername = isset($_POST['newUsername']) ? $_POST['newUsername'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (empty($newUsername) || empty($password)) {
            $response = ['success' => false, 'message' => 'All fields are required.'];
        } else {
            // Path to the JSON file
            $jsonFilePath = '../json/users.json';

            // Check if JSON file exists
            if (!file_exists($jsonFilePath)) {
                $response = ['success' => false, 'message' => 'User data not found.'];
            } else {
                // Read and decode JSON file
                $jsonData = file_get_contents($jsonFilePath);
                $users = json_decode($jsonData, true);

                // Find the current user
                $currentUser = null;
                foreach ($users as $index => $user) {
                    if ($user['username'] === $username) {
                        $currentUser = $user;
                        $currentUserIndex = $index;
                        break;
                    }
                }

                // Check if current user is found
                if (!$currentUser) {
                    $response = ['success' => false, 'message' => 'User not found.'];
                } else {
                    // Verify password
                    if (!password_verify($password, $currentUser['password'])) {
                        $response = ['success' => false, 'message' => 'Incorrect password.'];
                    } else {
                        // Update username
                        $users[$currentUserIndex]['username'] = $newUsername;

                        // Save updated users data to JSON file
                        file_put_contents($jsonFilePath, json_encode($users));

                        // Update session username
                        $_SESSION['username'] = $newUsername;

                        $response = ['success' => true];
                    }
                }
            }
        }
        echo '<script>';
        if ($response['success']) {
            echo 'alert("Profile updated successfully!");';
            echo 'document.getElementById("currentUsername").innerText = "' . htmlspecialchars($newUsername) . '";';
            echo 'window.location.href = "profile.php";';
        } else {
            echo 'alert("' . htmlspecialchars($response['message']) . '");';
        }
        echo '</script>';
    }
    ?>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-banner"></div>
            <div class="profile-info">
                <img src="../img/braver-blank-pfp.jpg" class="profile-pic">
                <div class="profile-details">
                    <div class="profile-text">
                        <h1 id="currentUsername"><?php echo htmlspecialchars($username); ?></h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-edit">
            <h2>Edit Profile Details:</h2>
            <form id="editProfileForm" method="post" onsubmit="return editProfile()">
                <label for="newUsername">Enter New Username:</label>
                <input type="text" id="newUsername" name="newUsername" placeholder="new username" required><br><br>
                <label for="password">Enter Password:</label>
                <input type="password" id="password" name="password" placeholder="password" required><br><br>
                <button type="submit">Update Profile</button>
            </form>
        </div>
    </div>

    <script>
        function editProfile() {
            const newUsername = document.getElementById('newUsername').value;
            const password = document.getElementById('password').value;

            if (!newUsername || !password) {
                alert('Please fill in all fields.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
