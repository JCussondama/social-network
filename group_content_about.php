<div style="min-height: 400px;width:100%;background-color: white;text-align: center;">
	<div style="padding: 20px;max-width:350px;display: inline-block;">
		<form method="post" enctype="multipart/form-data">

  						
			<?php
		 
				$settings_class = new Settings();

				$settings = $settings_class->get_settings($group_data['userid']);

				if(is_array($settings)){
 
					echo "<br>About the Group:<br>
							<div id='textbox' style='height:200px;border:none;' >".htmlspecialchars($settings['about'])."</div>
						";

					 
				}
				
			?>

		</form>
	</div>
</div>