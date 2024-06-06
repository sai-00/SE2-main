<?php
// Connecting to MySQL database
$con = mysqli_connect("localhost", "root", "", "social_network");

// Function to insert posts
function insertPost() {
    global $con;
    global $user_id;

    if (isset($_POST['sub'])) {
        $content = htmlentities($_POST['content']);
        $upload_image = $_FILES['upload_image']['name'];
        $image_tmp = $_FILES['upload_image']['tmp_name'];
        $random_number = rand(1, 100);

        if (strlen($content) > 250) {
            echo "<script>alert('Please use 250 words or less.')</script>";
            echo "<script>window.open('home.php','_self')</script>";
        } else {
            if (strlen($upload_image) > 0 && strlen($content) >= 1) {
                move_uploaded_file($image_tmp, "imagepost/$upload_image.$random_number");

                $insert = "INSERT INTO posts (user_id, post_content, upload_image, post_date) 
                           VALUES ('$user_id', '$content', '$upload_image.$random_number', NOW())";

                $run = mysqli_query($con, $insert);

                if ($run) {
                    echo "<script>alert('Your post has been updated.')</script>";
                    echo "<script>window.open('home.php','_self')</script>";

                    $update = "UPDATE users SET posts = 'yes' WHERE user_id = '$userid'";
                    $run_update = mysqli_query($con, $update);
                }
                exit();
            } else {
                if ($upload_image == '' && $content == '') {
                    echo "<script>alert('Error occurred while uploading.')</script>";
                    echo "<script>window.open('home.php','_self')</script>";
                } else {
                    if ($content == '') {
                        move_uploaded_file($image_tmp, "imagepost/$upload_image.$random_number");

                        $insert = "INSERT INTO posts (user_id, post_content, upload_image, post_date) 
                                   VALUES ('$user_id', 'No content', '$upload_image.$random_number', NOW())";

                        $run = mysqli_query($con, $insert);

                        if ($run) {
                            echo "<script>alert('Your post has been updated.')</script>";
                            echo "<script>window.open('home.php','_self')</script>";

                            $update = "UPDATE users SET posts = 'yes' WHERE user_id = '$userid'";
                            $run_update = mysqli_query($con, $update);
                        }
                        exit();
                    } else {
                        $insert = "INSERT INTO posts (user_id, post_content, post_date) 
                                   VALUES ('$user_id', 'No image', NOW())";

                        $run = mysqli_query($con, $insert);

                        if ($run) {
                            echo "<script>alert('Your post has been updated.')</script>";
                            echo "<script>window.open('home.php','_self')</script>";

                            $update = "UPDATE users SET posts = 'yes' WHERE user_id = '$userid'";
                            $run_update = mysqli_query($con, $update);
                        }
                    }
                }
            }
        }
    }
}
?>
