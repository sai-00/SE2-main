<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Profile</title>
    <link rel="stylesheet" href="../css/site_layout.css">
    <link rel="stylesheet" href="../css/profile.css">
    <script src="../js/nav.js"></script>
    <style>
        .logo a
        {
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

    <div class="profile-header">
                <div class="profile-banner"><img src="#" alt="banner"></div>
                <div class="profile-info">
                    <img src="#" alt="Profile" class="profile-pic">
                    <div class="profile-details">
                        <div class="profile-text">
                            <h1>Username</h1>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                        </div>
                        <button class="edit-profile-btn" onclick="editProfile()">Edit Profile</button>
                    </div>
                </div>
            </div>

        <script>
            function editProfile()
            {
                    
            }
        </script>
</body>
</html>
