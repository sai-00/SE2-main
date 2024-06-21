<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check === false) {
        die("File is not an image.");
    }

    // Check file size (limit to 5MB)
    if ($_FILES['image']['size'] > 5000000) {
        die("Sorry, your file is too large.");
    }

    // Allow certain file formats
    if ($imageFileType !== 'jpg' && $imageFileType !== 'png' && $imageFileType !== 'jpeg' && $imageFileType !== 'gif') {
        die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    }

    // Check if $uploadFile is set to write
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        die("Sorry, there was an error uploading your file.");
    }

    // Save the text
    $text = htmlspecialchars($_POST['text']);

    // Save the post data (e.g., to a database or file)
    // For simplicity, saving to a file
    $postData = [
        'image' => $uploadFile,
        'text' => $text,
    ];

    file_put_contents('posts.json', json_encode($postData) . PHP_EOL, FILE_APPEND);

    echo "The file " . basename($_FILES['image']['name']) . " has been uploaded.";
} else {
    echo "Invalid request.";
}
?>
