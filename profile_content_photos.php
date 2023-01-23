<div style="min-height: 400px;width:100%;background-color: white;text-align: center;">
	<div style="padding: 20px;">
	<?php

		$DB = new Database();
		$sql = "select image,postid from posts where has_image = 1 && userid = $user_data[userid] order by id desc limit 30";
		$images = $DB->read($sql);

		$image_class = new Image();

		if(is_array($images)){

			foreach ($images as $image_row) {
				# code...
				echo "<a href='".ROOT."single_post/$image_row[postid]' >";
				echo "<img src='" . ROOT . $image_class->get_thumb_post($image_row['image']) . "' style='width:150px;margin:10px;' />";
				echo "</a>";
			}

		}else{

			echo "No images were found!";
		}


	?>

	</div>
</div>