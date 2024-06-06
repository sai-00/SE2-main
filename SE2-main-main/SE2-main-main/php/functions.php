</php

$con = mysqli_connect("localhost","root","","social_network")

function insterPos(){
	if(isset($_POST['sub'])){
		global $con;
		global $user_id;

		$content = htmlentities($_POST['content']);
		$upload_image = $_FILES['upload_image']['name'];
		$image_tmp = $_FILES['upload_image']['tmp_name'];
		$random_number = randd(1,100);

		if(strlen($content) > 250){
			echo "<script> alert ('please use 250 words or less') </script>";
			echo "<script> window.open('home.php','_self')</script>";
		}else{
			if(strlen($upload_image) >=  && strlen($content) >= 1){
				
				move_uploaded_files($image_tmp, "imagepost/$upload_image.$random_number")
				
				$insert = "insert into posts (user_id, post_content, upload_image, post_data,) 
				values('$user_id', '$content', '$upload_image.$random_number', NOW())";

				$run = mysqli_query($con, $insert);

				if($run){
					echo "<script>alert('your posted updated a moment ago')</script>";
					echo "<script> window.open('home.php','_self')</script>";

					$update = "update users set posts = 'yes' where user_id='userid'";
					$run_update = mysqli_query($con, $update);
				}
				exit();
			}else{
				if($upload_image = '' && $content = ''){
					echo "<script> alert('error occured while uploading')</script>";
					echo "<script> window.open('home.php','_self')</script>";
				}else{
					if($content = ''){
						move_uploaded_file($image_tmp, "imagepost/$upload_image.$random_number");

						$insert = "insert into posts (user_id, post_content, upload_image, post_data) 
						values('$user_id', '$content', '$upload_image.$random_number', NOW())";					}
						
						$run = mysqli_query($con, $insert);

				if($run){
					echo "<script>alert('your posted updated a moment ago')</script>";
					echo "<script> window.open('home.php','_self')</script>";

					$update = "update users set posts = 'yes' where user_id='$userid'";
					$run_update = mysqli_query($con, $update);
				}
				exit();
				}else{
					$insert = "insert into posts (user_id, post_content, upload_image, post_data) 
						values('$user_id', 'No', NOW())";					}
						
						$run = mysqli_query($con, $insert);

				if($run){
					echo "<script>alert('your posted updated a moment ago')</script>";
					echo "<script> window.open('home.php','_self')</script>";

					$update = "update users set posts = 'yes' where user_id='$userid'";
					$run_update = mysqli_query($con, $update)''
				}

				}
			}
		}
	}
}
?>