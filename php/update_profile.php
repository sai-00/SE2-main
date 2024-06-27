<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    exit("Unauthorized");
}

// Get the current username from session
$currentUsername = $_SESSION['username'];

// Validate and sanitize input
$newUsername = isset($_POST['newUsername']) ? trim($_POST['newUsername']) : '';
$newPassword = isset($_POST['newPassword']) ? password_hash($_POST['newPassword'], PASSWORD_DEFAULT) : '';

if (empty($newUsername) || empty($newPassword)) {
    http_response_code(400);
    exit("Username and password cannot be empty.");
}

// Load existing users from JSON file
$usersFile = '../json/users.json';
$users = json_decode(file_get_contents($usersFile), true);

if (!$users) {
    http_response_code(500);
    exit("Failed to load user data.");
}

// Find the user by current username and update
$updated = false;
foreach ($users as &$user) {
    if ($user['username'] === $currentUsername) {
        $user['username'] = $newUsername;
        $user['password'] = $newPassword;
        $updated = true;
        break;
    }
}

// Save updated users back to JSON file
if ($updated && file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT))) {
    // Update session with new username
    $_SESSION['username'] = $newUsername;
    
    // Return success response with updated username
    http_response_code(200);
    echo json_encode(['username' => $newUsername, 'message' => 'Profile updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update profile']);
}
?>
