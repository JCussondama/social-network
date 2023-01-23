<?php 

session_start();

if(isset($_SESSION['mybook_userid']))
{
	$_SESSION['mybook_userid'] = NULL;
	unset($_SESSION['mybook_userid']);

}

header("Location:" . ROOT ."login");
die;
