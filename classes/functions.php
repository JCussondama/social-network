<?php

function pagination_link(){
	
	$page_number = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  	$page_number = ($page_number < 1) ? 1 : $page_number;

	$arr['next_page'] = "";
	$arr['prev_page'] = "";

	//get current url
	//$url = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
	$url = ROOT . $_GET['url'];
	$url .= "?";

	$next_page_link = $url;
	$prev_page_link = $url;
	$page_found = false;

	$num = 0;
	foreach ($_GET as $key => $value) {
		# code...
		if($key == 'url')
		{
			continue;
		}
		
		$num++;
		
		if($num == 1){
			if($key == "page"){
				
				$next_page_link .= $key ."=" . ($page_number + 1);
				$prev_page_link .= $key ."=" . ($page_number - 1);
				$page_found = true;
			}else{
				$next_page_link .= $key ."=" . $value;
				$prev_page_link .= $key ."=" . $value;
			}

		}else{
			if($key == "page"){
				
				$next_page_link .= "&" . $key ."=" . ($page_number + 1);
				$prev_page_link .= "&" . $key ."=" . ($page_number - 1);
				$page_found = true;

			}else{
				$next_page_link .= "&" . $key ."=" . $value;
				$prev_page_link .= "&" . $key ."=" . $value;
			}
		}
		
	}

	$arr['next_page'] = $next_page_link;
	$arr['prev_page'] = $prev_page_link;

	if(!$page_found){

		$arr['next_page'] = $next_page_link . "&page=2";
		$arr['prev_page'] = $prev_page_link . "&page=1";
	}
	
	return $arr;
}

function i_own_content($row){

	$myid = $_SESSION['mybook_userid'];
	
	//profiles
	if(isset($row['gender']) && $row['type'] == "profile" && $myid == $row['userid']){

		return true;
	}

	//groups
	if(isset($row['gender']) && $row['type'] == "group" && $myid == $row['owner']){

		return true;
	}

	//messages
	if(isset($row['sender']) && $myid == $row['sender']){

		return true;
	}

	//comments and posts
	if(isset($row['postid'])){

		if($myid == $row['userid']){
			return true;
		}else{

			$Post = new Post();
			$one_post = $Post->get_one_post($row['parent']);

			if($myid == $one_post['userid']){
				return true;
			}

			//groups posts moderator or added admin
			$DB = new Database();
			$groupid = $one_post['owner'];

			$sql = "select * from group_members where (role = 'admin' || role = 'moderator') && groupid = '$groupid' limit 1";
			$check = $DB->read($sql);

			if(is_array($check)){
				return true;
			}

			//group owner
			$groupid = $row['userid'];
			$sql = "select * from users where owner = '$myid' && userid = '$groupid'  limit 1";
			$check = $DB->read($sql);

			if(is_array($check)){
				return true;
			}


		}
	}
 
	return false;
}

function tag($postid,$new_post_text = "")
{

	$DB = new Database();
	$sql = "select * from posts where postid = '$postid' limit 1";
	$mypost = $DB->read($sql);

	if(is_array($mypost)){
		$mypost = $mypost[0];

		if($new_post_text != ""){
			$old_post = $mypost;
			$mypost['post'] = $new_post_text;
		}

		$tags = get_tags($mypost['post']);
		foreach ($tags as $tag) {
			# code...
			$sql = "select * from users where tag_name = '$tag' limit 1";
			$tagged_user = $DB->read($sql);
			if(is_array($tagged_user)){

				$tagged_user = $tagged_user[0];

				if($new_post_text != ""){
					$old_tags = get_tags($old_post['post']);
					if(!in_array($tagged_user['tag_name'], $old_tags)){
						add_notification($_SESSION['mybook_userid'],"tag",$mypost,$tagged_user['userid']);
					}
				}else{
					
					//tag
					add_notification($_SESSION['mybook_userid'],"tag",$mypost,$tagged_user['userid']);
 				}

			}
		}
	}
}

function add_notification($userid,$activity,$row,$tagged_user = '')
{

	$row = (object)$row;
	$userid = esc($userid);
	$activity = esc($activity);
	$content_owner = $row->userid;

		if($tagged_user != ""){
			$content_owner = $tagged_user;
		}

	$date = date("Y-m-d H:i:s");
	$contentid = 0;
	$content_type = "";

	if(isset($row->postid)){
		$contentid = $row->postid;
		$content_type = "post";

		if($row->parent > 0){
			$content_type = "comment";
		}
	}
	
	if(isset($row->gender)){
		$content_type = $row->type;
		$contentid = $row->userid;

		if($content_type == "group"){
			$content_owner = $row->owner;
		}
	}

	$query = "insert into notifications (userid,activity,content_owner,date,contentid,content_type) 
	values ('$userid','$activity','$content_owner','$date','$contentid','$content_type')";
	$DB = new Database();
	$DB->save($query);

}

function content_i_follow($userid,$row)
{

	$row = (object)$row;

	$userid = esc($userid);
 	$date = date("Y-m-d H:i:s");
	$contentid = 0;
	$content_type = "";

	if(isset($row->postid)){
		$contentid = $row->postid;
		$content_type = "post";

		if($row->parent > 0){
			$content_type = "comment";
		}
	}
	
	if(isset($row->gender)){
		$content_type = "profile";
	}

	$query = "insert into content_i_follow (userid,date,contentid,content_type) 
	values ('$userid','$date','$contentid','$content_type')";
	$DB = new Database();
	$DB->save($query);
}

function esc($value)
{

	return addslashes($value);
}

function notification_seen($id)
{

	$notification_id = addslashes($id);
	$userid = $_SESSION['mybook_userid'];
	$DB = new Database();

	$query = "select * from notification_seen where userid = '$userid' && notification_id = '$notification_id' limit 1";
	$check = $DB->read($query);

	if(!is_array($check)){

		$query = "insert into notification_seen (userid,notification_id) 
		values ('$userid','$notification_id')";
		
		$DB->save($query);
	}
}

function check_notifications()
{
	$number = 0;

	$userid = $_SESSION['mybook_userid'];
	$DB = new Database();

	$follow = array();

	//check content i follow
	$sql = "select * from content_i_follow where disabled = 0 && userid = '$userid' limit 100";
	$i_follow = $DB->read($sql);
	if(is_array($i_follow)){
		$follow = array_column($i_follow, "contentid");
	}

	if(count($follow) > 0){

		$str = "'" . implode("','", $follow) . "'";
		$query = "select * from notifications where (userid != '$userid' && content_owner = '$userid') || (contentid in ($str)) order by id desc limit 30";
	}else{

		$query = "select * from notifications where userid != '$userid' && content_owner = '$userid' order by id desc limit 30";
	}
 							
 	$data = $DB->read($query);

 	if(is_array($data)){

 		foreach ($data as $row) {
 			# code...
	 		$query = "select * from notification_seen where userid = '$userid' && notification_id = '$row[id]' limit 1";
			$check = $DB->read($query);

			if(!is_array($check)){

				$number++;
			}
		}
	}

	return $number;

}

function check_tags($text)
{
	$str = "";
	$words = explode(" ", $text);
	if(is_array($words) && count($words)>0)
	{
		$DB = new Database();
		foreach ($words as $word) {

			if(preg_match("/@[a-zA-Z_0-9\Q,.\E]+/", $word)){
				
				$word = trim($word,'@');
				$word = trim($word,',');
				$tag_name = esc(trim($word,'.'));

				$query = "select * from users where tag_name = '$tag_name' limit 1";
				$user_row = $DB->read($query);

				if(is_array($user_row)){
					$user_row = $user_row[0];
					$str .= "<a href='".ROOT."profile/$user_row[userid]'>@" . $word . "</a> ";
				}else{

					$str .= htmlspecialchars($word) . " ";
				}
 			
			}else{
				$str .= htmlspecialchars($word) . " ";
			}
		}

	}

	if($str != ""){
		return $str;
	}

	return htmlspecialchars($text);
}

function get_tags($text)
{
	$tags = array();
	$words = explode(" ", $text);
	if(is_array($words) && count($words)>0)
	{
		$DB = new Database();
		foreach ($words as $word) {

			if(preg_match("/@[a-zA-Z_0-9\Q,.\E]+/", $word)){
				
				$word = trim($word,'@');
				$word = trim($word,',');
				$tag_name = esc(trim($word,'.'));

				$query = "select * from users where tag_name = '$tag_name' limit 1";
				$user_row = $DB->read($query);

				if(is_array($user_row)){
					
					$tags[] = $word;
				}
 			
			}
		}

	}
 
	return $tags;
}


if(isset($_SESSION['mybook_userid'])){
	set_online($_SESSION['mybook_userid']);
}

function set_online($id){

	if(!is_numeric($id)){
		return;
	}

	$online = time();
	$query = "update users set online = '$online' where userid = '$id' limit 1";
	$DB = new Database();
	$DB->save($query);

}

function show($data){

	echo "<pre>";
	print_r($data);
	echo "</pre>";
}

$URL = split_url2();
function split_url2()
{
	$url = isset($_GET['url']) ? $_GET['url'] : "index";
	$url = explode("/", filter_var(trim($url,"/"),FILTER_SANITIZE_URL));

	return $url;
}

function split_url_from_string($str)
{
	$url = isset($str) ? $str : "index";
	$url = explode("/", filter_var(trim($url,"/"),FILTER_SANITIZE_URL));

	return $url;
}

function check_messages(){

	$DB = new Database();
	$me = esc($_SESSION['mybook_userid']);
 
	$query = "select * from messages where (receiver = '$me' && deleted_receiver = 0 && seen = 0) limit 100";
	$data = $DB->read($query);

	if(is_array($data)){
		return count($data);
	}
	return 0;
}

function check_seen_thread($msgid){

	$DB = new Database();
	$me = esc($_SESSION['mybook_userid']);
 
	$query = "select * from messages where (receiver = '$me' && deleted_receiver = 0 && seen = 0 && msgid = '$msgid') limit 100";
	$data = $DB->read($query);

	if(is_array($data)){
		return count($data);
	}
	return 0;
}


function is_invited($groupid,$userid){

	$userid = addslashes($userid);
	$groupid = addslashes($groupid);

	$DB = new Database();

	$query = "select userid from group_invites where groupid = '$groupid' && userid = '$userid' && disabled = 0 limit 1";
	$data = $DB->read($query);
	if(is_array($data)){
		return true;
	}
	return false;
}


function group_access($userid,$group_data,$access){

	//$admins = array($group_data["owner"]); 
	//$moderators = array($group_data["owner"]); 
	//$members = array($group_data["owner"]);

	//$group_type = $group_data["group_type"];
	if($group_data["owner"] == $userid){
		return true;
	}

	$DB = new Database();
	$groupid = $group_data["userid"];

	if($access == 'member'){
	
		$query = "select userid from group_members where groupid = '$groupid' && userid = '$userid' && (role = 'member' || role = 'admin' || role = 'moderator') limit 1";
		$data = $DB->read($query);
		if(is_array($data)){
			return true;
		}
		return false;
	}else
	if($access == 'moderator'){
		$query = "select userid from group_members where groupid = '$groupid' && userid = '$userid' && (role = 'admin' || role = 'moderator') limit 1";
		$data = $DB->read($query);
		if(is_array($data)){
			return true;
		}
		return false;
	}else
	if($access == 'admin'){
		$query = "select userid from group_members where groupid = '$groupid' && userid = '$userid' && (role = 'admin') limit 1";
		$data = $DB->read($query);
		if(is_array($data)){
			return true;
		}
		return false;
	}else
	if($access == 'request'){

		$query = "select * from group_requests where userid = '$userid' && groupid = '$groupid' && disabled = 0 limit 1";
		$data = $DB->read($query);
		if(is_array($data)){
			return true;
		}
		return false;
	}

	return false;
	//admin, moderator, member, nonmember
}

