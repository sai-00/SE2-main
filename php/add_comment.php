<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

$username = $_SESSION['username'];
$postId = $_POST['comment_post_id'] ?? '';
$commentText = $_POST['comment_text'] ?? '';

if (empty($postId) || empty($commentText)) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$postsFile = '../json/posts.json';
if (!file_exists($postsFile)) {
    echo json_encode(['error' => 'Posts file not found']);
    exit;
}

$posts = json_decode(file_get_contents($postsFile), true);
foreach ($posts as &$post) {
    if ($post['id'] === $postId) {
        $comment = ['username' => $username, 'text' => htmlspecialchars($commentText)];
        $post['comments'][] = $comment;
        file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT));
        echo json_encode($comment);
        exit;
    }
}

echo json_encode(['error' => 'Post not found']);
?>
