<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Home</title>
    <link rel="stylesheet" href="../css/site_layout.css">
    <link rel="stylesheet" href="../css/users.css">
    <script src="../js/nav.js"></script>
    <style>
    /* user page */
    .breadcrumb { 
        margin-top: 100px; 
        margin-left: 20px;
        font-size: 1.2em;
    }

    .breadcrumb a {
        text-decoration: none;
        color: #333;
    }   

    .breadcrumb a:hover {
        text-decoration: underline;
        color: #6660c1; 
        transition: 0.3s;
    }      

    .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 60px); /* Full height minus the navbar height */
        }

        .user-profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            width: 100%;
        }

        .user-profile img {
            max-width: 100%;
            border-radius: 8px;
        }

        .user-profile h2 {
            margin-top: 10px;
        }

        .user-profile p {
            margin: 10px 0;
        }

        .user-profile button {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
        }

        .user-profile button:hover {
            background-color: #6660c1;
            transition: 0.3s;
        }

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

    <div class="breadcrumb">
        <a href="users.php"> < Back</a>
    </div>

    <section class="main-content">
        <div id="user-profile" class="user-profile">
        </div>
    </section>
    
    <script type="module">
        // Fetch the user ID from the URL
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('id');

        // Fetch the user list from local storage
        const userArray = localStorage.getItem("userList");
        const users = JSON.parse(userArray);

        // Find the user by ID
        const user = users.find(u => u.id == userId);

        const userProfile = document.getElementById("user-profile");

        if (user) {
            userProfile.innerHTML = `
                <img src="${user.image}" alt="User Image">
                <h2>${user.name}</h2>
                <p>${user.description}</p>
            `;
        } else {
            userProfile.innerHTML = "<p>User not found.</p>";
        }
    </script>
</body>
</html>
