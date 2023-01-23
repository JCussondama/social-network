<div style="min-height: 400px;width:100%;background-color: white;text-align: center;">

	<br>
	<a href="<?=ROOT?>create_group">
		<input id="post_button" type="button" value="Create Group" style="float:none;margin-right:10px;background-color: #1b9186;width:auto;">
	</a>

	<div style="padding: 20px;">
	<?php
 
		$image_class = new Image();
		$group_class = new Group();
		$user_class = new User();

		$groups = $group_class->get_my_groups($user_data['userid']);

		if(is_array($groups)){

			foreach ($groups as $group) {
				# code...
				$FRIEND_ROW = $user_class->get_user($group['userid']);
				include("group.inc.php");
			}

		}else{

			echo "No groups were found!";
		}


	?>

	</div>
</div>