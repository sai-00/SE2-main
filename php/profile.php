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

        .profile-edit a, .post-history a
        {
            text-decoration: none;
            color: transparent;
        }

        .profile-edit button
        {
            background-color: #7ca4e6;
            color: white;
            margin: 10px;
            margin-top: 20px;
            padding: 15px;
            width: 100%; 
            border: none; 
            cursor: pointer;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
        }

        .post-history button
        {
            background-color: #7ca4e6;
            color: white;
            margin-top: 20px;
            padding: 15px;
            width: 55%; 
            border: none; 
            cursor: pointer;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
        }

        .profile-edit button:hover, .post-history button:hover
        {
            background-color: #6660c1; 
            transition: 0.3s;
        }

        .post-history
        {
            margin-left: 120px;
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

        <div class="post-history">
            <strong>View post history:</strong><br>
            <a href="post_history.php"><button>Post history</button></a>
            <a href="comment_history.php"><button>Comment history</button></a>
        </div>

        <div class="profile-edit">
            <strong>Edit Profile Details:</strong><br>
                <a href="profile_usernameedit.php"><button>Update username</button></a>
                <a href="profile_passwordedit.php"><button>Update password</button></a>
        </div>
    </div>


        <script>
    function editProfile() {
        const newUsername = document.getElementById('newUsername').value;
        const newPassword = document.getElementById('newPassword').value;

        // Example validation (you should add more robust validation)
        if (!newUsername || !newPassword) {
            alert('Please fill in all fields.');
            return false;
        }

        // Example: Sending data to the server (you can use fetch or AJAX)
        const formData = new FormData();
        formData.append('newUsername', newUsername);
        formData.append('newPassword', newPassword);

        fetch('update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Handle success or display a message
            alert('Profile updated successfully!');
            
            // Update username displayed on the page
            document.getElementById('currentUsername').innerText = newUsername;
        })
        .catch(error => {
            console.error('Error updating profile:', error);
            alert('Failed to update profile. Please try again.');
        });

        return false; // Prevent form submission
    }
</script>



</body>
</html>
