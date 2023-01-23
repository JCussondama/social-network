<div style="min-height: 400px;width:100%;background-color: white;text-align: center;">
	<div style="padding: 20px;max-width:350px;display: inline-block;">
		<form method="post" enctype="multipart/form-data">

  						
			<?php
		 
				$settings_class = new Settings();

				$settings = $settings_class->get_settings($group_data['userid']);

				if(is_array($settings)){

					echo "<input type='text' id='textbox' name='first_name' value='".htmlspecialchars($settings['first_name'])."' placeholder='Group name' />";
 
					echo "<select id='textbox' name='group_type' style='height:30px;width:104%;'>

							<option>".htmlspecialchars($settings['group_type'])."</option>
							<option>Public</option>
							<option>Private</option>
						</select>";

 					echo "<br>About me:<br>
							<textarea id='textbox' style='height:200px;' name='about'>".htmlspecialchars($settings['about'])."</textarea>
						";

					echo '<input id="post_button" type="submit" value="Save">';
				}
				
			?>

		</form>
	</div>
</div>