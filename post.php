
	<div id="post">
		<div>
		
			<?php 

				$image = "images/user_male.jpg";
				if($ROW_USER['gender'] == "Female")
				{
					$image = "images/user_female.jpg";
				}else
				if($ROW_USER['type'] == "group")
				{
					$image = $image_class->get_thumb_profile("images/cover_image.jpg");
				}
				

				if(file_exists($ROW_USER['profile_image']))
				{
					$image = $image_class->get_thumb_profile($ROW_USER['profile_image']);
				}else
				if($ROW_USER['type'] == "group" && file_exists($ROW_USER['cover_image']))
				{
					$image = $image_class->get_thumb_profile($ROW_USER['cover_image']);
				}
			?>

			<img src="<?php echo ROOT . $image ?>" style="width: 75px;margin-right: 4px;border-radius: 50%;">
		</div>
		<div style="width: 100%;">
			<div style="font-weight: bold;color: #405d9b;width: 100%;">
				<?php 
					echo "<a href='" . ROOT . "profile/$ROW[userid]'>";
					echo htmlspecialchars($ROW_USER['first_name']) . " " . htmlspecialchars($ROW_USER['last_name']); 
					echo "</a>";

					if($ROW['is_profile_image'])
					{
						$pronoun = "his";
						if($ROW_USER['gender'] == "Female")
						{
							$pronoun = "her";
						}
						echo "<span style='font-weight:normal;color:#aaa;'> updated $pronoun profile image</span>";

					}

					if($ROW['is_cover_image'])
					{
						$pronoun = "his";
						if($ROW_USER['gender'] == "Female")
						{
							$pronoun = "her";
						}else
						if($ROW_USER['type'] == "group")
						{
							$pronoun = "their";
						}

						
						echo "<span style='font-weight:normal;color:#aaa;'> updated $pronoun cover image</span>";

					}


				?>

			</div>
			
			<?php echo check_tags($ROW['post']) ?>

			<br><br>

			<?php 

				if(file_exists($ROW['image']))
				{

					$ext = pathinfo($ROW['image'],PATHINFO_EXTENSION);
					$ext = strtolower($ext);

					if($ext == "jpeg" || $ext == "jpg"){

						$post_image = $image_class->get_thumb_post($ROW['image']);

						echo '<a href="' . ROOT . 'single_post/' . $ROW['postid'] . '">';
						echo "<img src='" . ROOT . "$post_image' style='width:80%;' />";
						echo '</a>';

					}elseif($ext == "mp4"){

						echo "<video controls style='width:100%' >
							<source src='" . ROOT . "$ROW[image]' type='video/mp4' >
						</video>";
 						
					}

					
				}
				
			?>

		<br/><br/>
		<?php 
			$likes = "";

			$likes = ($ROW['likes'] > 0) ? "(" .$ROW['likes']. ")" : "" ;

		?>
		<a onclick="like_post(event)" href="<?=ROOT?>like/post/<?php echo $ROW['postid'] ?>">Like<?php echo $likes ?></a> . 

		<?php 
			$comments = "";

			if($ROW['comments'] > 0){

				$comments = "(" . $ROW['comments'] . ")";
			}

		?>

		<a href="<?=ROOT?>single_post/<?php echo $ROW['postid'] ?>">Comment<?php echo $comments ?></a> . 

		<span style="color: #999;">
			
			<?php echo Time::get_time($ROW['date']) ?>

		</span>

		<?php 

			$ext = pathinfo($ROW['image'],PATHINFO_EXTENSION);
			$ext = strtolower($ext);
  
			if($ROW['has_image'] && ($ext == "jpeg" || $ext == "jpg")){

				echo "<a href='".ROOT."image_view/$ROW[postid]' >";
				echo ". View Full Image . ";
				echo "</a>";
			}
		?>

		<span style="color: #999;float:right">
			
			<?php 

				$post = new Post();

				if(i_own_content($ROW)){

					echo "
					<a href='".ROOT."edit/$ROW[postid]'>
		 				Edit
					</a> .

					 <a href='".ROOT."delete/$ROW[postid]' >
		 				Delete
					</a>";
				}
 
			 ?>

		</span>

			<?php 

				$i_liked = false;

				if(isset($_SESSION['mybook_userid'])){

					$DB = new Database();

					$sql = "select likes from likes where type='post' && contentid = '$ROW[postid]' limit 1";
					$result = $DB->read($sql);
					if(is_array($result)){

						$likes = json_decode($result[0]['likes'],true);

						$user_ids = array_column($likes, "userid");
		 
						if(in_array($_SESSION['mybook_userid'], $user_ids)){
							$i_liked = true;
						}
					}

				}

			 	echo "<a id='info_$ROW[postid]' href='".ROOT."likes/post/$ROW[postid]'>";
			 	
			 	if($ROW['likes'] > 0){

			 		echo "<br/>";

			 		if($ROW['likes'] == 1){

			 			if($i_liked){
			 				echo "<div style='text-align:left;'>You liked this post </div>";
			 			}else{
			 				echo "<div style='text-align:left;'> 1 person liked this post </div>";
			 			}
			 		}else{

			 			if($i_liked){

			 				$text = "others";
			 				if($ROW['likes'] - 1 == 1){
			 					$text = "other";
			 				}
			 				echo "<div style='text-align:left;'> You and " . ($ROW['likes'] - 1) . " $text liked this post </div>";
			 			}else{
			 				echo "<div style='text-align:left;'>" . $ROW['likes'] . " other liked this post </div>";
			 			}
			 		}


			 	}
			 	echo "</a>";
			?>
		</div>
	</div>

<script type="text/javascript">
	

	function ajax_send(data,element){

		var ajax = new XMLHttpRequest();

		ajax.addEventListener('readystatechange', function(){

			if(ajax.readyState == 4 && ajax.status == 200){

				response(ajax.responseText,element);
			}
			
		});

  		data = JSON.stringify(data);

		ajax.open("post","<?=ROOT?>ajax.php",true);
		ajax.send(data);

	}

	function response(result,element){

		if(result != ""){

			var obj = JSON.parse(result);
			if(typeof obj.action != 'undefined'){

				if(obj.action == 'like_post'){

					var likes = "";

					if(typeof obj.likes != 'undefined'){
						likes = (parseInt(obj.likes) > 0) ? "Like(" +obj.likes+ ")" : "Like" ;
						element.innerHTML = likes;
					}

					if(typeof obj.info != 'undefined'){
						var info_element = document.getElementById(obj.id);
						info_element.innerHTML = obj.info;
					}
				}
			}
		}
	}

	function like_post(e){

		e.preventDefault();
		var link = e.target.href;

		var data = {};
		data.link = link;
		data.action = "like_post";
		ajax_send(data,e.target);
	}

</script>