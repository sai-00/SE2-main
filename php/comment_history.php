<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Retrieve username from session
$username = $_SESSION['username'];

// Define file paths
$postsFile = '../json/posts.json';

// Initialize posts data array
$postsData = [];

// Load posts data if the file exists
if (file_exists($postsFile)) {
    $postsJson = file_get_contents($postsFile);
    $postsData = json_decode($postsJson, true);
}

// Collect comments made by the current user
$userComments = [];
if (is_array($postsData)) {
    foreach ($postsData as $post) {
        foreach (array_reverse($post['comments']) as $comment) { // Reverse the comments array here
            if ($comment['username'] === $username) {
                $userComments[] = [
                    'postText' => $post['text'],
                    'commentText' => $comment['text'],
                    'postImage' => $post['image'],
                    'postTags' => $post['tags']
                ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Comment History</title>
    <link rel="stylesheet" href="../css/site_layout.css">
    <style>
        .main-content {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .breadcrumbs { 
            margin-top: -50px;
            font-size: 1.2em;
            position: absolute;
        }

        .breadcrumbs a {
            color: #333;
            text-decoration: none;
            padding: 8px;
        }   

        .breadcrumbs a:hover {
            text-decoration: underline;
            color: #6660c1; 
            transition: 0.3s;
        }

        .comments {
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow-y: scroll;
            max-height: 80vh;
            padding-right: 10px;
        }

        .comment {
            display: flex;
            flex-direction: column;
            background-color: #dba181;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: background-color 0.3s;
        }

        .comment img {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .comment p {
            margin: 0 0 10px;
        }

        .comment strong {
            display: block;
            margin-bottom: 5px;
        }

        img {
            width: 60%;
            height: auto;
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

    <section class="main-content">
        <div class="breadcrumbs">
            <a href="profile.php"><- Back to Profile</a>
        </div>
        <?php if (!empty($userComments)): ?>
            <div class="comments">
                <?php foreach ($userComments as $comment): ?>
                    <div class="comment">
                        <center><img src="<?php echo htmlspecialchars($comment['postImage']); ?>" alt="Post Image"></center>
                        <p><strong>Commented on:</strong> <?php echo htmlspecialchars($comment['postText']); ?></p>
                        <p><strong>Your Comment:</strong> <?php echo htmlspecialchars($comment['commentText']); ?></p>
                        <p><strong>Tags:</strong> <?php echo implode(', ', array_map('htmlspecialchars', $comment['postTags'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>You have not made any comments yet.</p>
        <?php endif; ?>
    </section>
    <script>
        function logout() {
        window.location.href = "login.php";
        sessionStorage.clear();
    }

    document.addEventListener('DOMContentLoaded', function() {
        var logoutLink = document.getElementById('logout');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default link behavior
                logout(); // Call logout function
            });
        } else {
            console.error('Logout link element not found.');
        }
    });
    </script>
</body>
</html>
