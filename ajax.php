<?php 

include("classes/autoload.php");

$data = file_get_contents("php://input");
if($data != ""){
	$data = json_decode($data);
}

if(isset($data->action) && $data->action == "like_post")
{
	include "ajax/like.ajax.php";
}
