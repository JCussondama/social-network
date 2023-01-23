<?php 
ini_set("display_errors",1);

function split_url()
{
	$url = isset($_GET['url']) ? $_GET['url'] : "home";
	$url = explode("/", filter_var(trim($url,"/"),FILTER_SANITIZE_URL));

	return $url;
}

//create root variable
$root = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
$root = trim(str_replace("index.php", "", $root),"/");

define("ROOT", $root . "/");

$URL = split_url();

if(file_exists($URL[0] . ".php")){
	require($URL[0] . ".php");
}else{

	require("404.php");
}
