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

        .main {
            padding-top: 30px;
            padding-left: 60px; 
            font-size: 2rem;
            padding-right: 60px;
        }

        .logo a {
            text-decoration: none;
            color: white;
        }

        .post-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
            margin: 20px;
            max-width: 300px;
            display: inline-block;
            vertical-align: top;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);  
        }

        .post-card img {
            max-width: 100%;
            display: block;
            cursor: pointer;
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
            background-color: #7ca4e6;
            color: white;
            margin: 10px;
            margin-top: 20px;
            padding: 10px;
            width: 50%; 
            border: none; 
            cursor: pointer;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
        }

        .post-card button:hover {
            background-color: #6660c1;
            transition: 0.3s;
        }

        .posts-container {
            margin: 20px 0;
            text-align: center;
            max-height: 600px; /* Adjust the height as needed */
            overflow-y: auto; /* Enable vertical scrolling */
            padding-right: 10px; /* Add some padding to avoid clipping */
        }

        .modal a {
            color: inherit; 
            text-decoration: underline;
            cursor: pointer;
        }

        .modal a:hover{
            color: #7ca4e6;
            transition: 0.3s;
        }

    .modal 
    {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.9);
    }

    .modal img 
    {
        width: 60%;
        height: auto;
    }

    .modal-content {
        margin: 10% auto;
        padding: 20px;
        width: 100%;
        max-width:50%;
        background-color: #fefefe;
        position: relative;
        background-color: #dba181;
        border-radius: 20px;
    }

    .close {
        color: #1b1815;
        position: absolute;
        top: 10px;
        right: 25px;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
    }

    .post {
        margin: 20px;
        border: 1px solid #ccc;
        padding: 10px;
        width: 300px;
        cursor: pointer;
    }

    .post img {
        max-width: 100%;
        height: auto;
        display: block;
        margin-bottom: 10px;
    }

    .logo a
    {
        text-decoration: none;
        color: white;
    }

    button
    {
        background-color: #7ca4e6;
        color: white;
        margin-bottom: 15px;
        padding: 15px;
        width: 30%; 
        border: none; 
        cursor: pointer;
        text-align: center;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
    }

    button:hover {
        background-color: #6660c1; 
        transition: 0.3s;
    }

    #commentText
    {
        border-radius: 20px;
        font-size: 15px;
        margin-bottom: 10px;
    }

    hr
    {
        color: #1b1815;
    }

    .form-parent
    {
        display: flex;
    }

    .form-text
    {
        flex: 1;
    }

    .form-button
    {
        flex: 1;
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

    <section class="main">
        <?php if ($user): ?>
            <h1><?php echo htmlspecialchars($user['username']); ?></h1>
        <?php else: ?>
            <p>User not found.</p>
        <?php endif; ?>
        <hr>
    </section>

    <section class="posts-container">
        <h2>Posts</h2>
        
        <?php if (count($user_posts) > 0): ?>
            <?php foreach ($user_posts as $post): ?>
                <div class="post-card" data-post-id="<?php echo htmlspecialchars($post['id']); ?>" onclick="openModal('<?php echo htmlspecialchars($post['image']); ?>', '<?php echo htmlspecialchars($post['text']); ?>', '<?php echo htmlspecialchars($post['id']); ?>', '<?php echo htmlspecialchars(json_encode($post['comments'])); ?>', '<?php echo htmlspecialchars($post['username']); ?>', '<?php echo htmlspecialchars(json_encode($post['tags'])); ?>')">
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image">
                    <div class="post-content">
                        <?php echo isset($post['text']) ? htmlspecialchars($post['text']) : 'No Title'; ?>
                        <p><strong>Tags:</strong> <?php echo implode(', ', array_map('htmlspecialchars', $post['tags'])); ?></p>
                        <p><strong>Comments:</strong> <span class="comment-count"><?php echo count($post['comments']); ?></span></p>
                        <button>Comment</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts available.</p>
        <?php endif; ?>
    </section>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <center><img id="modalImage" src="" alt="Modal Image"></center>
            <p id="modalText"></p>
            <p id="modalUsername"></p>
            <p id="modalTags"></p>
            <form id="commentForm" method="post" action="index.php" onsubmit="submitComment(event)">
                <input type="hidden" name="comment_post_id" id="commentPostId">
                <div class="form-parent">
                        <div class="form-text"> 
                            <textarea name="comment_text" id="commentText" rows="2" cols="50" placeholder="Add a comment" required></textarea>
                        </div>
                        <div class="form-button">
                            <button type="submit">Comment</button>
                        </div>
                    </div>
            </form>
            <br><hr>
            <h3>Comments</h3>
            <div id="commentsSection"></div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('myModal');
        const modalImage = document.getElementById('modalImage');
        const modalText = document.getElementById('modalText');
        const modalUsername = document.getElementById('modalUsername');
        const modalTags = document.getElementById('modalTags');
        const commentsSection = document.getElementById('commentsSection');
        const commentPostId = document.getElementById('commentPostId');
        const commentForm = document.getElementById('commentForm');
        const commentText = document.getElementById('commentText');

        window.openModal = function(imageSrc, text, postId, comments, username, tags) {
            console.log('Opening modal with image:', imageSrc, 'text:', text, 'username:', username, 'tags:', tags);

            modalImage.src = imageSrc;
            modalText.textContent = text;
            modalUsername.innerHTML = `Posted by: <a href="userpage.php?id=${username}">${username}</a>`;
            modalTags.textContent = "Tags: " + JSON.parse(tags).join(', ');
            commentPostId.value = postId;

            commentsSection.innerHTML = ''; // Clear existing comments
            const commentsArray = JSON.parse(comments).reverse(); // Reverse the comments array
            commentsArray.forEach(function(comment) {
                const commentDiv = document.createElement('div');
                commentDiv.innerHTML = `<strong><a href="userpage.php?id=${comment.username}">${comment.username}</a></strong>: ${comment.text}`; // Username bold and clickable
                commentsSection.appendChild(commentDiv);
            });

            modal.style.display = "block";
        };

        window.closeModal = function() {
            modal.style.display = "none";
        };

        window.onclick = function(event) {
            if (event.target === modal) {
                closeModal();
            }
        };

        window.submitComment = function(event) {
            event.preventDefault(); // Prevent form from submitting normally
            const postId = commentPostId.value;
            const comment = commentText.value;

            if (comment.trim() === '') {
                alert('Comment cannot be empty');
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_comment.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const newComment = JSON.parse(xhr.responseText);
                    const commentDiv = document.createElement('div');
                    commentDiv.innerHTML = `<strong><a href="userpage.php?id=${newComment.username}">${newComment.username}</a></strong>: ${newComment.text}`; // Username bold and clickable
                    commentsSection.insertBefore(commentDiv, commentsSection.firstChild); // Insert the new comment at the top
                    commentText.value = ''; // Clear the textarea

                    // Update the comment count on the post card
                    const postTile = document.querySelector(`.post-card[data-post-id='${postId}']`);
                    const commentCountElement = postTile.querySelector('.comment-count');
                    commentCountElement.textContent = parseInt(commentCountElement.textContent) + 1;
                }
            };
            xhr.send('comment_post_id=' + encodeURIComponent(postId) + '&comment_text=' + encodeURIComponent(comment));
        };
    });
    </script>
</body>
</html>
