<?php 

Class Notification extends Database
{

	function get_notifications()
	{
		$result = $this->read("select * from users");
		print_r($result);
	}
}