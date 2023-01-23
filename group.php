<?php

	include("classes/autoload.php");

	$login = new Login();
	$_SESSION['mybook_userid'] = isset($_SESSION['mybook_userid']) ? $_SESSION['mybook_userid'] : 0;
	
	$USER = $login->check_login($_SESSION['mybook_userid'],false);
 
 	$group_data = array();
 	
 	if(isset($URL[1]) && is_numeric($URL[1])){

	 	$group = new Group();
	 	$g_data = $group->get_group($URL[1]);

	 	if(is_array($g_data)){
	 		$group_data = $g_data[0];
	 	}

 	}
 	
	//posting starts here
	if($_SERVER['REQUEST_METHOD'] == "POST")
	{

		include("change_image.php");
		
		if(isset($_POST['first_name'])){

			if(group_access($_SESSION['mybook_userid'],$group_data,'admin')){
				$settings_class = new Settings();
				$settings_class->save_settings($_POST,$group_data['userid']);
			}

			header("Location: " . ROOT . "group/" . $group_data['userid'] . "/settings");
			die;

		}elseif(isset($_POST['post'])){

			$post = new Post();
			$id = $_SESSION['mybook_userid'];
			$owner = $group_data['userid'];
			$result = $post->create_post($id, $_POST,$_FILES,$owner);
			
			if($result == "")
			{
				
				header("Location: " . ROOT . "group/" . $group_data['userid']);
				die;
			}else
			{

				echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>";
				echo "<br>The following errors occured:<br><br>";
				echo $result;
				echo "</div>";
			}
		}
			
	}

	if(count($group_data) > 0){

		//collect posts
		$post = new Post();
		$id = $group_data['userid'];
		
		$posts = $post->get_posts($id,'group');

		//collect friends
		$user = new User();
	 	
		$friends = $user->get_following($group_data['userid'],"user");

		$image_class = new Image();

		//check if this is from a notification
		if(isset($URL[3])){
			notification_seen($URL[3]);
		}

	}



?>

<!DOCTYPE html>
	<html>
	<head>
		<title>Group | Mybook</title>
	</head>

	<style type="text/css">
		
		#blue_bar{

			height: 50px;
			background-color: #405d9b;
			color: #d9dfeb;

		}

		#search_box{

			width: 400px;
			height: 20px;
			border-radius: 5px;
			border:none;
			padding: 4px;
			font-size: 14px;
			background-image: url(search.png);
			background-repeat: no-repeat;
			background-position: right;

		}

		#textbox{

			width: 100%;
			height: 20px;
			border-radius: 5px;
			border:none;
			padding: 4px;
			font-size: 14px;
			border: solid thin grey;
			margin:10px;
 
		}

		#profile_pic{

			width: 150px;
			margin-top: -300px;
			border-radius: 50%;
			border:solid 2px white;
		}

		#menu_buttons{

			width: 100px;
			display: inline-block;
			margin:2px;
		}

		#friends_img{

			width: 75px;
			float: left;
			margin:8px;

		}

		#friends_bar{

			background-color: white;
			min-height: 400px;
			margin-top: 20px;
			color: #aaa;
			padding: 8px;
		}

		#friends{

		 	clear: both;
		 	font-size: 12px;
		 	font-weight: bold;
		 	color: #405d9b;
		}

		textarea{

			width: 100%;
			border:none;
			font-family: tahoma;
			font-size: 14px;
			height: 60px;

		}

		#post_button{

			float: right;
			background-color: #405d9b;
			border:none;
			color: white;
			padding: 4px;
			font-size: 14px;
			border-radius: 2px;
			width: 50px;
			min-width: 50px;
			cursor: pointer;
		}
 
 		#post_bar{

 			margin-top: 20px;
 			background-color: white;
 			padding: 10px;
 		}

 		#post{

 			padding: 4px;
 			font-size: 13px;
 			display: flex;
 			margin-bottom: 20px;
 		}

	</style>

	<body style="font-family: tahoma; background-color: #d0d8e4;">

		<br>
		<?php include("header.php"); ?>
 
 		<?php if(count($group_data) > 0): ?>

		<!--change cover image area-->
 		<div id="change_cover_image" style="display:none;position:absolute;width: 100%;height: 100%;background-color: #000000aa;">
 			<div style="max-width:600px;margin:auto;min-height: 400px;flex:2.5;padding: 20px;padding-right: 0px;">
 					
 					<form method="post" action="<?=ROOT?>group/<?=$group_data['userid']?>/cover"  enctype="multipart/form-data">
	 					<div style="border:solid thin #aaa; padding: 10px;background-color: white;">

	 						<input type="file" name="file"><br>
	 						<input id="post_button" type="submit" style="width:120px;" value="Change">
	 						<br>
							<div style="text-align: center;">
								<br><br>
							<?php

 	 							echo "<img src='" . ROOT . "$group_data[cover_image]' style='max-width:500px;' >";
								 
	 						?>
							</div>
	 					</div>
  					</form>

 				</div>
 		</div>

		<!--cover area-->
		<div style="width: 800px;margin:auto;min-height: 400px;">
			
			<div style="background-color: white;text-align: center;color: #405d9b">

					<?php 

						$image = "images/cover_image.jpg";
						if(file_exists($group_data['cover_image']))
						{
							$image = $image_class->get_thumb_cover($group_data['cover_image']);
						}
					?>

				<img src="<?php echo ROOT . $image ?>" style="width:100%;">


				<span style="font-size: 12px;">
					
					<?php if(i_own_content($group_data)):?>
					
						<a onclick="show_change_cover_image(event)" style="text-decoration: none;color:#f0f;" href="<?=ROOT?>change_profile_image/cover">Change Cover</a>
					
					<?php endif; ?>

				</span>
				<br>
					<div style="font-size: 20px;color: black;">
						<a href="<?=ROOT?>group/<?php echo $group_data['userid'] ?>">
							<?php echo $group_data['first_name'] ?><br>
							<span style="font-size: 12px;">[<?php echo $group_data['group_type'] ?> Group]</span>
 						</a>
 					 
						<br>
 							<?php if(!group_access($_SESSION['mybook_userid'],$group_data,'member')):?>
								<?php if(!group_access($_SESSION['mybook_userid'],$group_data,'request')):?>
	  							
		  							<a href="<?=ROOT?>join/<?=$group_data['userid']?>">
										<input id="post_button" type="button" value="Join Group" style="margin-right:10px;background-color: #821b91;width:auto;">
									</a>
								<?php else: ?>

									<input id="post_button" type="button" value="Request sent" style="margin-right:10px;background-color: #821b91;width:auto;">
								<?php endif; ?>
							<?php endif; ?>

							<?php if(group_access($_SESSION['mybook_userid'],$group_data,'member')):?>
							<a href="<?=ROOT?>group/<?php echo $group_data['userid'] ?>/invite">
								<input id="post_button" type="button" value="Invite" style="margin-right:10px;background-color: #1b9186;width:auto;">
							</a>
							<?php endif; ?>
							
 
					</div>
				<br>
				<br>

					<?php 
						$members_count = $group->get_members($group_data['userid']);
						$members_str = "";
						if(is_array($members_count)){
							$members_str = "(".count($members_count).")";
						}
					?>

				<a href="<?=ROOT?>group/<?php echo $group_data['userid'] ?>"><div id="menu_buttons">Discussion</div></a>
				<a href="<?=ROOT?>group/<?php echo $group_data['userid'] ?>/about"><div id="menu_buttons">About</div></a>
				<a href="<?=ROOT?>group/<?php echo $group_data['userid'] ?>/members"><div id="menu_buttons">Members <?=$members_str?></div></a>
				<a href="<?=ROOT?>group/<?php echo $group_data['userid'] ?>/photos"><div id="menu_buttons">Photos</div></a>
				
				<?php if(group_access($_SESSION['mybook_userid'],$group_data,'moderator')):?>
					<?php 
						$requests_count = $group->get_requests($group_data['userid']);
						$requests_str = "";
						if(is_array($requests_count)){
							$requests_str = "(".count($requests_count).")";
						}
					?>

					<a href="<?=ROOT?>group/<?php echo $group_data['userid'] ?>/requests"><div id="menu_buttons">Requests <?=$requests_str?></div></a>
				<?php endif;?>

				<?php 
					if(group_access($_SESSION['mybook_userid'],$group_data,'admin')){
						
 						echo '<a href="'.ROOT. 'group/'.$group_data['userid'].'/settings"><div id="menu_buttons">Settings</div></a>';
					}
				?>
			</div>

			<!--below cover area-->
	 
	 		<?php 

	 			$section = "default";

	 			if(isset($URL[2])){

	 				$section = $URL[2];
	 			}

	 			if($group_data['group_type'] == 'private' && !group_access($_SESSION['mybook_userid'],$group_data,'member')){
	 				$section = "denied";
	 			}

	 			if($section == "default" || $section == "cover"){

	 				include("group_content_default.php");
	 			 
	 			}elseif($section == "requests"){
	 				
	 				include("group_content_requests.php");
				}elseif($section == "invite"){
	 				
	 				include("group_content_invite.php");
				}elseif($section == "invited"){
	 				
	 				include("group_content_invited.php");

	 			}elseif($section == "members"){
	 				
	 				include("group_content_members.php");
	 			
	 			}elseif($section == "about"){

	 				include("group_content_about.php");

	 			}elseif($section == "settings"){

	 				include("group_content_settings.php");

	 			}elseif($section == "photos"){

	 				include("group_content_photos.php");
	 			}elseif($section == "groups"){

	 				include("group_content_groups.php");
	 			}elseif($section == "denied"){

	 				include("group_content_denied.php");
	 			}



	 		?>

		</div>
	<?php else: ?>

		<div style="background-color: grey;color: white;padding: 1em;text-align: center;margin:1em;">That group was not found!
			<br><br>
			<a href="<?=ROOT?>profile/<?=$_SESSION['mybook_userid']?>/groups">
				Go to groups
			</a>
		</div>
	
	<?php endif; ?>

	</body>
</html>

<script type="text/javascript">
	
	function show_change_profile_image(event){

		event.preventDefault();
		var profile_image = document.getElementById("change_profile_image");
		profile_image.style.display = "block";
	}


	function hide_change_profile_image(){

		var profile_image = document.getElementById("change_profile_image");
		profile_image.style.display = "none";
	}

	
	function show_change_cover_image(event){

		event.preventDefault();
		var cover_image = document.getElementById("change_cover_image");
		cover_image.style.display = "block";
	}


	function hide_change_cover_image(){

		var cover_image = document.getElementById("change_cover_image");
		cover_image.style.display = "none";
	}


	window.onkeydown = function(key){

		if(key.keyCode == 27){

			//esc key was pressed
			hide_change_profile_image();
			hide_change_cover_image();
		}
	}

	
</script>