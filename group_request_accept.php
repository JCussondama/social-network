<?php 

include("classes/autoload.php");

	$login = new Login();
	$user_data = $login->check_login($_SESSION['mybook_userid']);

 
if(isset($_SERVER['HTTP_REFERER'])){

	$return_to = $_SERVER['HTTP_REFERER'];
}else{
	$return_to = "profile.php";
}


$groupid = isset($URL[1]) ? $URL[1] : null;
$userid = isset($URL[2]) ? $URL[2] : null;
$action = isset($URL[3]) ? $URL[3] : null;


$group_class = new Group();
$group_class->accept_request($groupid,$userid,$action);

header("Location: " . $return_to);
die;