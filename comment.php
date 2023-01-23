
	<div id="post" style="background-color: #eee;">
		<div>
		
			<?php 

				$image = "images/user_male.jpg";
				if($ROW_USER['gender'] == "Female")
				{
					$image = "images/user_female.jpg";
				}

				if(file_exists($ROW_USER['profile_image']))
				{
					$image = $image_class->get_thumb_profile($ROW_USER['profile_image']);
				}
  
			?>

			<img src="<?php echo ROOT . $image ?>" style="width: 75px;margin-right: 4px;border-radius: 50%;">
		</div>
		<div style="width: 100%;">
			<div style="font-weight: bold;color: #405d9b;width: 100%;">
				<?php 
					echo "<a href='".ROOT."profile/$COMMENT[userid]'>";
					echo htmlspecialchars($ROW_USER['first_name']) . " " . htmlspecialchars($ROW_USER['last_name']); 
					echo "</a>";

					if($COMMENT['is_profile_image'])
					{
						$pronoun = "his";
						if($ROW_USER['gender'] == "Female")
						{
							$pronoun = "her";
						}
						echo "<span style='font-weight:normal;color:#aaa;'> updated $pronoun profile image</span>";

					}

					if($COMMENT['is_cover_image'])
					{
						$pronoun = "his";
						if($ROW_USER['gender'] == "Female")
						{
							$pronoun = "her";
						}
						echo "<span style='font-weight:normal;color:#aaa;'> updated $pronoun cover image</span>";

					}


				?>
			</div>
			
			<?php echo check_tags($COMMENT['post']) ?>

			<br><br>

			<?php 

				if(file_exists($COMMENT['image']))
				{

					$post_image = ROOT . $image_class->get_thumb_post($COMMENT['image']);

					echo "<img src='$post_image' style='width:80%;' />";
				}
				
			?>

		<br/><br/>
		<?php 
			$likes = "";

			$likes = ($COMMENT['likes'] > 0) ? "(" .$COMMENT['likes']. ")" : "" ;

		?>
		<a href="<?=ROOT?>like/post/<?php echo $COMMENT['postid'] ?>">Like<?php echo $likes ?></a> . 

 
		<span style="color: #999;">
			
			<?php echo $COMMENT['date'] ?>

		</span>

		<?php 

			if($COMMENT['has_image']){

				echo "<a href='".ROOT."image_view/$COMMENT[postid]' >";
				echo ". View Full Image . ";
				echo "</a>";
			}
		?>

		<span style="color: #999;float:right">
			
			<?php 

				$post = new Post();

				if($post->i_own_post($COMMENT['postid'],$_SESSION['mybook_userid'])){

					echo "
					<a href='".ROOT."edit/$COMMENT[postid]'>
		 				Edit
					</a> . ";

					 
				}

				if(i_own_content($COMMENT)){

					echo "<a href='".ROOT."delete/$COMMENT[postid]' >
		 				Delete
					</a>";
				}
 
			 ?>

		</span>

			<?php 

				$i_liked = false;

				if(isset($_SESSION['mybook_userid'])){

					$DB = new Database();

					$sql = "select likes from likes where type='post' && contentid = '$COMMENT[postid]' limit 1";
					$result = $DB->read($sql);
					if(is_array($result)){

						$likes = json_decode($result[0]['likes'],true);

						$user_ids = array_column($likes, "userid");
		 
						if(in_array($_SESSION['mybook_userid'], $user_ids)){
							$i_liked = true;
						}
					}

				}

			 	if($COMMENT['likes'] > 0){

			 		echo "<br/>";
			 		echo "<a href='".ROOT."likes/post/$COMMENT[postid]'>";

			 		if($COMMENT['likes'] == 1){

			 			if($i_liked){
			 				echo "<div style='text-align:left;'>You liked this comment </div>";
			 			}else{
			 				echo "<div style='text-align:left;'> 1 person liked this comment </div>";
			 			}
			 		}else{

			 			if($i_liked){

			 				$text = "others";
			 				if($COMMENT['likes'] - 1 == 1){
			 					$text = "other";
			 				}
			 				echo "<div style='text-align:left;'> You and " . ($COMMENT['likes'] - 1) . " $text liked this comment </div>";
			 			}else{
			 				echo "<div style='text-align:left;'>" . $COMMENT['likes'] . " other liked this comment </div>";
			 			}
			 		}

			 		echo "</a>";

			 	}
			?>
		</div>
	</div>