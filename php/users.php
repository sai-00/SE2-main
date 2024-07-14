<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

// Path to the users.json file
$file = '../json/users.json';

// Fetch the users data from JSON file
$users = [];
if (file_exists($file)) {
    $users = json_decode(file_get_contents($file), true);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Users</title>
    <link rel="stylesheet" href="../css/site_layout.css">
    <link rel="stylesheet" href="../css/users.css">
    <script src="../js/nav.js"></script>
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

    <div id="user-list" class="user-list"></div>

    <script>
        const usersData = <?php echo json_encode($users); ?>;
        console.log("Users data:", usersData); // Debugging line
    </script>
    <script src="../js/users.js"></script>
</body>
</html>
