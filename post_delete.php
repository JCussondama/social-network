
	<div id="post">
		<div>
		
			<?php 

				$image = "images/user_male.jpg";
				if($ROW_USER['gender'] == "Female")
				{
					$image = "images/user_female.jpg";
				}

				$image_class = new Image();
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

					echo htmlspecialchars($ROW_USER['first_name']) . " " . htmlspecialchars($ROW_USER['last_name']); 

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
						}
						echo "<span style='font-weight:normal;color:#aaa;'> updated $pronoun cover image</span>";

					}


				?>
			</div>
			
			<?php echo htmlspecialchars($ROW['post']) ?>

			<br><br>

			<?php 

				if(file_exists($ROW['image']))
				{

					$ext = pathinfo($ROW['image'],PATHINFO_EXTENSION);
					$ext = strtolower($ext);

					if($ext == "jpeg" || $ext == "jpg"){

						$post_image = $image_class->get_thumb_post($ROW['image']);

 						echo "<img src='" . ROOT . "$post_image' style='width:80%;' />";
 
					}elseif($ext == "mp4"){

						echo "<video controls style='width:100%' >
							<source src='" . ROOT . "$ROW[image]' type='video/mp4' >
						</video>";
 						
					}
				}
				
			?>
  		
		</div>
	</div>