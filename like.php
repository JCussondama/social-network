<?php 

include("classes/autoload.php");

	$login = new Login();
	$user_data = $login->check_login($_SESSION['mybook_userid']);

 
if(isset($_SERVER['HTTP_REFERER'])){

	$return_to = $_SERVER['HTTP_REFERER'];
}else{
	$return_to = "profile.php";
}


$_GET['type'] = isset($URL[1]) ? $URL[1] : null;
$_GET['id'] = isset($URL[2]) ? $URL[2] : null;

	if(isset($_GET['type']) && isset($_GET['id'])){
		
		if(is_numeric($_GET['id'])){

			$allowed[] = 'post';
			$allowed[] = 'user';
			$allowed[] = 'comment';

			if(in_array($_GET['type'], $allowed)){

				$post = new Post();
				$user_class = new User();
				$post->like_post($_GET['id'],$_GET['type'],$_SESSION['mybook_userid']);

				if($_GET['type'] == "user"){
					$user_class->follow_user($_GET['id'],$_GET['type'],$_SESSION['mybook_userid']);
				}
			}

		}
		
	}


header("Location: ". $return_to);
die;
