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
    <style>
        #user-searchbar
        {
            margin-top: 8px;
            border-radius: 20px;
            padding: 10px;
            width: 100%;
            margin-right: 10px;
            margin-left: 260px;
        }
        #clear-search 
        {
            border-radius: 20px;
            padding: 10px;
            margin-left: 260px;
            width: 30%;
            cursor: pointer;
            background-color: #7ca4e6;
            color: white;
            border: none;
        }
        #clear-search:hover 
        {
            background-color: #6660c1;
            transition: 0.3s;
        }
        .parent-search
        {
            display: flex;
        }
        .child-search
        {
            flex: 1;
            margin: 10px;
            margin-left: 30px;
        }
    </style>
</head>
<body>
    <nav class="top-navbar">
        <div class="logo">
            <a href="index.php"><strong>Pawpedia</strong></a>
        </div>

        <div class="user-search">
            <div class="parent-search">
                <div class="child-search">
                    <input type="text" placeholder="Search User" id="user-searchbar" data-users='<?php echo json_encode($users); ?>'>
                </div>
                <div class="child-search">
                    <button id="clear-search"> Clear Search </button>
                </div>
            </div>
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
        console.log("Users data:", usersData); 
    </script>
    <script src="../js/users.js"></script>
</body>
</html>
