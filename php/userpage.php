<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_GET['id'] ?? '';

$file = '../json/users.json';
$users = json_decode(file_get_contents($file), true);

$post_file = '../json/posts.json';
$posts = json_decode(file_get_contents($post_file), true);

$user = null;
foreach ($users as $u) {
    if ($u['username'] === $user_id) {
        $user = $u;
        break;
    }
}

// Fetch posts by the user
$user_posts = [];
foreach ($posts as $post) {
    if ($post['username'] === $user_id) {
        $user_posts[] = $post;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Home</title>
    <link rel="stylesheet" href="../css/site_layout.css">
    <link rel="stylesheet" href="../css/modal.css">
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

        .logo a {
            text-decoration: none;
            color: white;
        }

        /* post cards */
        .post-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
            margin: 20px;
            max-width: 300px;
            display: inline-block;
            vertical-align: top;
        }

        .post-card img {
            max-width: 100%;
            display: block;
        }

        .post-card .post-content {
            padding: 10px;
        }

        .post-card h3 {
            margin: 0;
        }

        .post-card p {
            margin: 5px 0;
        }

        .post-card button {
            background-color: #6660c1;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .post-card button:hover {
            background-color: #333;
            transition: 0.3s;
        }

        .posts-container {
            margin: 20px 0;
            text-align: center;
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
            <?php if ($user): ?>
                <h2><?php echo htmlspecialchars($user['username']); ?></h2>
            <?php else: ?>
                <p>User not found.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="posts-container">
        <h2>Posts</h2>
        <?php if (count($user_posts) > 0): ?>
            <?php foreach ($user_posts as $post): ?>
                <div class="post-card">
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image">
                    <div class="post-content">
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p>Post ID: <?php echo htmlspecialchars($post['id']); ?></p>
                        <p>Posted by: <?php echo htmlspecialchars($post['username']); ?></p>
                        <p>Tags: <?php echo htmlspecialchars($post['tags']); ?></p>
                        <button>Like</button>
                        <p>Likes: <?php echo htmlspecialchars($post['likes']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts available.</p>
        <?php endif; ?>
    </section>
</body>
</html>
