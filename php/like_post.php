<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = isset($_POST['post_id']) ? htmlspecialchars($_POST['post_id']) : '';

    if (!empty($postId)) {
        $postsFile = '../json/posts.json';

        if (file_exists($postsFile)) {
            $posts = file($postsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $updatedPosts = [];
            $likeUpdated = false;

            foreach ($posts as $post) {
                $postData = json_decode($post, true);

                if ($postData && is_array($postData) && $postData['id'] === $postId) {
                    $postData['likes']++;
                    $likeUpdated = true;
                }

                $updatedPosts[] = json_encode($postData);
            }

            if ($likeUpdated) {
                file_put_contents($postsFile, implode(PHP_EOL, $updatedPosts) . PHP_EOL);
                echo json_encode(['success' => true, 'likes' => $postData['likes']]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
