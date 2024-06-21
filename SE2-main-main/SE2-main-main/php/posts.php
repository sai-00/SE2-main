<?php
$postsFile = 'posts.json';

if (!file_exists($postsFile)) {
    echo "No posts available.";
    exit;
}

$posts = file($postsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

echo "<h2>Posts</h2>";
foreach ($posts as $post) {
    $postData = json_decode($post, true);
    if ($postData) {
        echo "<div>";
        echo "<img src='" . htmlspecialchars($postData['image']) . "' alt='Post Image' style='width:300px;height:auto;'><br>";
        echo "<p>" . htmlspecialchars($postData['text']) . "</p>";
        echo "</div><br>";
    }
}
?>