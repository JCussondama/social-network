<?php 

class Post
{
	private $error = "";

	public function create_post($userid, $data, $files, $owner = 0)
	{
 		
		if(!empty($data['post']) || !empty($files['file']['name']) || isset($data['is_profile_image']) || isset($data['is_cover_image']))
		{

			$myimage = "";
			$has_image = 0;
			$is_cover_image = 0;
			$is_profile_image = 0;

			if(isset($data['is_profile_image']) || isset($data['is_cover_image']))
			{

				$myimage = $files;
				$has_image = 1;

				if(isset($data['is_cover_image']))
				{
					$is_cover_image = 1;
				}

				if(isset($data['is_profile_image']))
				{
					$is_profile_image = 1;
				}
			}else
			{

				if(!empty($files['file']['name']))
				{


					$folder = "uploads/" . $userid . "/";

						//create folder
						if(!file_exists($folder))
						{

							mkdir($folder,0777,true);
							file_put_contents($folder . "index.php", "");
						}
					
					$allowed[] = "image/jpeg";
					$allowed[] = "video/mp4";

					if(in_array($files['file']['type'], $allowed)){

						$image_class = new Image();

						$ext = pathinfo($files['file']['name'],PATHINFO_EXTENSION);
						$ext = strtolower($ext);

						$myimage = $folder . $image_class->generate_filename(15) . "." . $ext;
						move_uploaded_file($files['file']['tmp_name'], $myimage);

						if($ext == "jpg" || $ext == "jpeg"){
							$image_class->resize_image($myimage,$myimage,1500,1500);
						}

						$has_image = 1;
					}else{

						$this->error .= "The selected image is not a valid type. only jpegs allowed!<br>";
					}
				}
			}

			$post = "";
			if(isset($data['post'])){

				$post = addslashes($data['post']);
			}

			//add tagged users
			$tags = array();
			$tags = get_tags($post);
			$tags = json_encode($tags);

			if($this->error == ""){

				$postid = $this->create_postid();
				$parent = 0;
				$DB = new Database();

				if(isset($data['parent']) && is_numeric($data['parent'])){

					$parent = $data['parent'];
					$mypost = $this->get_one_post($data['parent']);

					if(is_array($mypost) && $mypost['userid'] != $userid){
						
						//follow this item
						content_i_follow($userid,$mypost);

						//add notification
						add_notification($_SESSION['mybook_userid'],"comment",$mypost);
					}

					$sql = "update posts set comments = comments + 1 where postid = '$parent' limit 1";
					$DB->save($sql);
				}

				$query = "insert into posts (owner,userid,postid,post,image,has_image,is_profile_image,is_cover_image,parent,tags) values ('$owner','$userid','$postid','$post','$myimage','$has_image','$is_profile_image','$is_cover_image','$parent','$tags')";
				$DB->save($query);

				//notify those that were tagged
				tag($postid);

			}
		}else
		{
			$this->error .= "Please type something to post!<br>";
		}

		return $this->error;
	}

	public function edit_post($data, $files)
	{

		if(!empty($data['post']) || !empty($files['file']['name']))
		{

			$myimage = "";
			$has_image = 0;

 				if(!empty($files['file']['name']))
				{


					$folder = "uploads/" . $userid . "/";

						//create folder
						if(!file_exists($folder))
						{

							mkdir($folder,0777,true);
							file_put_contents($folder . "index.php", "");
						}

						$image_class = new Image();

						$myimage = $folder . $image_class->generate_filename(15) . ".jpg";
						move_uploaded_file($_FILES['file']['tmp_name'], $myimage);

						$image_class->resize_image($myimage,$myimage,1500,1500);

					$has_image = 1;
				}
 
			$post = "";
			if(isset($data['post'])){

				$post = addslashes($data['post']);
			}

			$postid = addslashes($data['postid']);

			if($has_image){
				$query = "update posts set post = '$post', image = '$myimage' where postid = '$postid' limit 1";
			}else{
				$query = "update posts set post = '$post' where postid = '$postid' limit 1";
			}

			//notify those that were tagged
			tag($postid, $post);

			$DB = new Database();
			$DB->save($query);

		}else
		{
			$this->error .= "Please type something to post!<br>";
		}

		return $this->error;
	}

	

	public function get_posts($id,$post_type = "profile")
	{

		$page_number = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  		$page_number = ($page_number < 1) ? 1 : $page_number;

		$limit = 10;
 		$offset = ($page_number - 1) * $limit;

		$query = "select * from posts where parent = 0 and owner = 0 and userid = '$id' order by id desc limit $limit offset $offset";
		
		if($post_type == "group"){
			$query = "select * from posts where parent = 0 and owner = '$id' order by id desc limit $limit offset $offset";
		}

		$DB = new Database();
		$result = $DB->read($query);

		if($result)
		{
			return $result;
		}else
		{
			return false;
		}
	}


	public function get_comments($id)
	{

		$page_number = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  		$page_number = ($page_number < 1) ? 1 : $page_number;

		$limit = 10;
 		$offset = ($page_number - 1) * $limit;

		$query = "select * from posts where parent = '$id' order by id asc limit $limit offset $offset";

		$DB = new Database();
		$result = $DB->read($query);

		if($result)
		{
			return $result;
		}else
		{
			return false;
		}
	}

	public function get_one_post($postid)
	{

		if(!is_numeric($postid)){

			return false;
		}

		$query = "select * from posts where postid = '$postid' limit 1";

		$DB = new Database();
		$result = $DB->read($query);

		if($result)
		{
			return $result[0];
		}else
		{
			return false;
		}
	}


	public function delete_post($postid)
	{

		if(!is_numeric($postid)){
			
			return false;
		}

		$Post = new Post();
		$one_post = $Post->get_one_post($postid);

		$DB = new Database();
		$sql = "select parent from posts where postid = '$postid' limit 1";
		$result = $DB->read($sql);
		
		if(is_array($result)){

			if($result[0]['parent'] > 0){

				$parent = $result[0]['parent'];

				$sql = "update posts set comments = comments - 1 where postid = '$parent' limit 1";
				$DB->save($sql);
			}
		}
			

		$query = "delete from posts where postid = '$postid' limit 1";
		$DB->save($query);

		//delete any images and thumbnails
		if($one_post['image'] != "" && file_exists($one_post['image']))
		{
			unlink($one_post['image']);
		}

		if($one_post['image'] != "" && file_exists($one_post['image']. "_post_thumb"))
		{
			unlink($one_post['image']. "_post_thumb");
		}

		if($one_post['image'] != "" && file_exists($one_post['image']. "_cover_thumb"))
		{
			unlink($one_post['image']. "_cover_thumb");
		}

		//delete all comments
		$query = "delete from posts where parent = '$postid' ";
		$DB->save($query);


  
	}

	public function i_own_post($postid,$mybook_userid)
	{

		if(!is_numeric($postid)){
			
			return false;
		}

		$query = "select * from posts where postid = '$postid' limit 1";

		$DB = new Database();
		$result = $DB->read($query);
  		
  		if(is_array($result)){

  			if($result[0]['userid'] == $mybook_userid){

  				return true;
  			}
  		}

  		return false;
	}

	public function get_likes($id,$type){

		$DB = new Database();
		$type = addslashes($type);

		if(is_numeric($id)){
 
			//get like details
			$sql = "select likes from likes where type='$type' && contentid = '$id' limit 1";
			$result = $DB->read($sql);
			if(is_array($result)){

				$likes = json_decode($result[0]['likes'],true);
				return $likes;
			}
		}


		return false;
	}

	public function like_post($id,$type,$mybook_userid){


 			$DB = new Database();
 			
			//save likes details
			$sql = "select likes from likes where type='$type' && contentid = '$id' limit 1";
			$result = $DB->read($sql);
			if(is_array($result)){

				$likes = json_decode($result[0]['likes'],true);

				$user_ids = array_column($likes, "userid");
 
				if(!in_array($mybook_userid, $user_ids)){

					$arr["userid"] = $mybook_userid;
					$arr["date"] = date("Y-m-d H:i:s");

					$likes[] = $arr;

					$likes_string = json_encode($likes);
					$sql = "update likes set likes = '$likes_string' where type='$type' && contentid = '$id' limit 1";
					$DB->save($sql);

					//increment the right table
					$sql = "update {$type}s set likes = likes + 1 where {$type}id = '$id' limit 1";
					$DB->save($sql);

					if($type != "user"){
						$post = new Post();
						$single_post = $post->get_one_post($id);

						//add notification
						add_notification($_SESSION['mybook_userid'],"like",$single_post);
					}

				}else{

					$key = array_search($mybook_userid, $user_ids);
					unset($likes[$key]);

					$likes_string = json_encode($likes);
					$sql = "update likes set likes = '$likes_string' where type='$type' && contentid = '$id' limit 1";
					$DB->save($sql);

					//increment the right table
					$sql = "update {$type}s set likes = likes - 1 where {$type}id = '$id' limit 1";
					$DB->save($sql);

				}
				

			}else{

				$arr["userid"] = $mybook_userid;
				$arr["date"] = date("Y-m-d H:i:s");

				$arr2[] = $arr;

				$likes = json_encode($arr2);
				$sql = "insert into likes (type,contentid,likes) values ('$type','$id','$likes')";
				$DB->save($sql);

				//increment the right table
				$sql = "update {$type}s set likes = likes + 1 where {$type}id = '$id' limit 1";
				$DB->save($sql);
 
 				if($type != "user"){
	 				$post = new Post();
					$single_post = $post->get_one_post($id);

					//add notification
					add_notification($_SESSION['mybook_userid'],"like",$single_post);
				}
			}

	}


	private function create_postid()
	{

		$length = rand(4,19);
		$number = "";
		for ($i=0; $i < $length; $i++) { 
			# code...
			$new_rand = rand(0,9);

			$number = $number . $new_rand;
		}

		return $number;
	}
}