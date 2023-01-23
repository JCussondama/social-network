<div style="min-height: 400px;width:100%;background-color: white;text-align: center;">
	<div style="padding: 20px;">

	<?php if(group_access($_SESSION['mybook_userid'],$group_data,'moderator')):?>
	<?php
 
		$image_class = new Image();
		//$post_class = new Post();
		$user_class = new User();

		$requests = $group->get_requests($group_data['userid']);

		if(is_array($requests)){

			foreach ($requests as $request) {
				# code...
				$FRIEND_ROW = $user_class->get_user($request['userid']);
				include("user_group_request.inc.php");
			}

		}else{

			echo "No requests were found!";
		}

	?>
	<?php else: ?>
		You dont have access to this content!
	<?php endif; ?>
	</div>
</div>