<?php 

include("classes/autoload.php");

	$login = new Login();
	$user_data = $login->check_login($_SESSION['mybook_userid']);

 
if(isset($_SERVER['HTTP_REFERER'])){

	$return_to = $_SERVER['HTTP_REFERER'];
}else{
	$return_to = "profile.php";
}

$group = new Group();
$groupid = isset($URL[1]) ? $URL[1] : null;

$g_data = $group->get_group($groupid);

if(is_array($g_data)){
	$group_data = $g_data[0];
}

if(group_access($_SESSION['mybook_userid'],$group_data,'member')){

	$userid = isset($URL[2]) ? $URL[2] : null;
	$me = $_SESSION['mybook_userid'];

	$group->invite_to_group($groupid,$userid,$me);

}

header("Location: " . $return_to);
die;