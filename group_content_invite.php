<div style="min-height: 400px;width:100%;background-color: white;text-align: center;">
	<div style="padding: 20px;">
	<?php if(group_access($_SESSION['mybook_userid'],$group_data,'member')):?>
	<?php
 
		$image_class = new Image();
		$post_class = new Post();
		$user_class = new User();

		$followers = $group->get_invites($group_data['userid'],$USER['userid'],"user");

		if(is_array($followers)){

			foreach ($followers as $follower) {
				# code...
				$FRIEND_ROW = $user_class->get_user($follower['userid']);
				include("user_group_invite.php");
			}

		}else{

			echo "No followers to invite were found!";
		}


	?>
	<?php else: ?>
		You must be a member to invite others
	<?php endif; ?>
	</div>
</div>