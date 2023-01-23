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
$me = $_SESSION['mybook_userid'];

$group_class = new Group();
$group_class->join_group($groupid,$me);

header("Location: " . $return_to);
die;