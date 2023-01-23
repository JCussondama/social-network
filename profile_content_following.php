<div style="min-height: 400px;width:100%;background-color: white;text-align: center;">
	<div style="padding: 20px;">
	<?php
 
		$image_class = new Image();
		$post_class = new Post();
		$user_class = new User();

		$following = $user_class->get_following($user_data['userid'],"user");

		if(is_array($following)){

			foreach ($following as $follower) {
				# code...
				$FRIEND_ROW = $user_class->get_user($follower['userid']);
				include("user.php");
			}

		}else{

			echo "This user inst following anyone!";
		}


	?>

	</div>
</div>