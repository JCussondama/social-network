<div style="min-height: 400px;width:100%;background-color: white;text-align: center;">
	<div style="padding: 20px;">
	<?php if(group_access($_SESSION['mybook_userid'],$group_data,'member')):?>
	<?php
 
		$image_class = new Image();
		//$post_class = new Post();
		$user_class = new User();

		if(isset($_GET['remove_comfirmed']) && (group_access($_SESSION['mybook_userid'],$group_data,'admin'))){

			$group->remove_member($group_data['userid'],$_GET['remove_comfirmed']);

			echo "This user was successfuly removed from the group!!<br><br>";
			$FRIEND_ROW = $user_class->get_user($_GET['remove_comfirmed']);
			include("user.php");

			echo '<br><br>
			<a href="'.ROOT.'group/'.$group_data['userid'].'/members">
				<input id="post_button" type="button" value="Back" style="font-size:11px;margin-right:10px;background-color: #1b9186;width:auto;">
			</a>
			';
		}else
		if(isset($_GET['edit_access']) && (group_access($_SESSION['mybook_userid'],$group_data,'admin'))){

			if(isset($_POST['role']) && isset($_POST['userid'])){

				$group->edit_member_access($group_data['userid'],$_GET['edit_access'],$_POST['role']);
			}

				echo "<form method='post'>
				Change user access<br><br>
				<div style='background-color:orange;color:white;padding:1em;text-align:center;'>
				Warning! giving users admin access also gives them the power to remove you as admin</div>
				";
				$FRIEND_ROW = $user_class->get_user($_GET['edit_access']);
				include("user.php");

				$role = "Unknown";
				$role = $group->get_member_role($group_data['userid'],$_GET['edit_access']);
				echo '<br><br>
					<select name="role" style="padding:5px;width:200px;">
						<option>'.$role.'</option>
						<option>member</option>
						<option>moderator</option>
						<option>admin</option>
					</select>
					<input type="hidden" name="userid" value="'.htmlspecialchars($_GET['edit_access']).'">
				<br>
				
	 			<input id="post_button" type="submit" value="Save" style="font-size:11px;margin-right:10px;background-color: #91261b;width:auto;">
	 			<a href="'.ROOT.'group/'.$group_data['userid'].'/members">
					<input id="post_button" type="button" value="Cancel" style="float:left;font-size:11px;margin-right:10px;background-color: #1b9186;width:auto;">
				</a>
				</form>
				';
 
		}else
		if(isset($_GET['remove']) && (group_access($_SESSION['mybook_userid'],$group_data,'admin'))){

			echo "Are you sure you want to remove this user from the group??<br><br>";
			$FRIEND_ROW = $user_class->get_user($_GET['remove']);
			include("user.php");

			echo '<br><br>

			<a href="'.ROOT.'group/'.$group_data['userid'].'/members?remove_comfirmed='.$FRIEND_ROW['userid'].'">
				<input id="post_button" type="button" value="Remove" style="font-size:11px;margin-right:10px;background-color: #91261b;width:auto;">
			</a>
			<a href="'.ROOT.'group/'.$group_data['userid'].'/members">
				<input id="post_button" type="button" value="Cancel" style="float:left;font-size:11px;margin-right:10px;background-color: #1b9186;width:auto;">
			</a>
			';

		}else{

			$members = $group->get_members($group_data['userid']);

			if(is_array($members)){

				foreach ($members as $member) {
					# code...
					$FRIEND_ROW = $user_class->get_user($member['userid']);
					include("user_group_member.inc.php");
				}

			}else{

				echo "This group has no members";
			}
		}

	?>
	<?php else: ?>
		You dont have access to this content!
	<?php endif; ?>
	</div>
</div>