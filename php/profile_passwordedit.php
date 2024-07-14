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
        $currentPassword = isset($_POST['currentPassword']) ? $_POST['currentPassword'] : '';
        $newPassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : '';

        if (empty($currentPassword) || empty($newPassword)) {
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
                    // Verify current password
                    if (!password_verify($currentPassword, $currentUser['password'])) {
                        $response = ['success' => false, 'message' => 'Incorrect password.'];
                    } else {
                        // Hash the new password
                        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                        // Update password
                        $users[$currentUserIndex]['password'] = $hashedNewPassword;

                        // Save updated users data to JSON file
                        file_put_contents($jsonFilePath, json_encode($users));

                        $response = ['success' => true];
                    }
                }
            }
        }
        echo '<script>';
        if ($response['success']) {
            echo 'alert("Password updated successfully!");';
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
                <label for="currentPassword">Enter Old Password:</label>
                <input type="password" id="currentPassword" name="currentPassword" placeholder="current password" required><br><br>
                <label for="newPassword">Enter New Password:</label>
                <input type="password" id="newPassword" name="newPassword" placeholder="new password" required><br><br>
                <button type="submit">Update Password</button>
            </form>
        </div>
    </div>

    <script>
        function editProfile() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;

            if (!currentPassword || !newPassword) {
                alert('Please fill in all fields.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
