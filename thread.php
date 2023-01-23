<?php

	
	$color = "#eee";
	
	if(check_seen_thread($MESSAGE['msgid']) > 0){
		$color = "#f7e5e5";
	}

?>
	<div id="message_thread" style="background-color: <?=$color?>;position: relative;">
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
					echo "<a href='".ROOT."profile/$MESSAGE[msgid]'>";
					echo htmlspecialchars($ROW_USER['first_name']) . " " . htmlspecialchars($ROW_USER['last_name']); 
					echo "</a>";

				 
				?>
			</div>
			
			<?php echo check_tags($MESSAGE['message']) ?>
  
			<?php 

				if(file_exists($MESSAGE['file']))
				{

					$post_image = ROOT . $image_class->get_thumb_post($MESSAGE['file']);

					echo "<img src='$post_image' style='width:60px;' />";
				}
				
			?>

		<br/><br/>
		 
		<span style="color: #999;">
			
			<?php echo Time::get_time($MESSAGE['date']) ?>

		</span>
 
		</div>
		<a href="<?=ROOT?>messages/read/<?=$myid?>">
			<div style="cursor:pointer;border-top-right-radius:50%;border-bottom-right-radius:50%;background-color: #78d9d0;height:90%;width:50px;position:absolute;right:10px;top:4px;">
				<svg fill="white" style="position: absolute;left:50%;top:50%;transform: translate(-50%,-50%)" width="24" height="24" viewBox="0 0 24 24"><path d="M8.122 24l-4.122-4 8-8-8-8 4.122-4 11.878 12z"/></svg>
			</div>
		</a>
	</div>