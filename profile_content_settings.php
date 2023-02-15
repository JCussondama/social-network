<div style="min-height: 400px;width:100%;background-color: white;text-align: center;">
	<div style="padding: 20px;max-width:350px;display: inline-block;">
		<form method="post" enctype="multipart/form-data">

  						
			<?php
		 
				$settings_class = new Settings();

				$settings = $settings_class->get_settings($_SESSION['mybook_userid']);

				if(is_array($settings)){

					echo "<input type='text' id='textbox' name='Primeiro nome ' value='".htmlspecialchars($settings['first_name'])."' placeholder='Primeiro nome' />";
					echo "<input type='text' id='textbox' name='Último nome' value='".htmlspecialchars($settings['last_name'])."' placeholder='Último nome' />";

					echo "<select id='textbox' name='email' style='height:30px;'>

							<option>".htmlspecialchars($settings['gender'])."</option>
							<option>Masculino</option>
							<option>Femenino</option>
						</select>";

					echo "<input type='text' id='textbox' name='email'  value='".htmlspecialchars($settings['email'])."' placeholder='E-mail'/>";
					echo "<input type='password' id='textbox' name='Palavra Passe '  value='".htmlspecialchars($settings['password'])."' placeholder='Palavra-passe'/>";
					echo "<input type='password' id='textbox' name='Palavra Passe'  value='".htmlspecialchars($settings['password'])."' placeholder='Palavra-passe'/>";
					
					echo "<br>About me:<br>
							<textarea id='textbox' style='height:200px;' name='acerca'>".htmlspecialchars($settings['about'])."</textarea>
						";

					echo '<input id="post_button" type="submit" value="salvar">';
				}
				
			?>

		</form>
	</div>
</div>