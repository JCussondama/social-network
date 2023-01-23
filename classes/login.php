<?php

class Login
{

	private $error = "";
 
	public function evaluate($data)
	{

		$email = addslashes($data['email']);
		$password = addslashes($data['password']);

		$query = "select * from users where email = '$email' limit 1 ";

		$DB = new Database();
		$result = $DB->read($query);

		if($result)
		{

			$row = $result[0];

			if($this->hash_text($password) == $row['password'])
			{

				//create session data
				$_SESSION['mybook_userid'] = $row['userid'];

			}else
			{
				$this->error .= "wrong email or password<br>";
			}
		}else
		{

			$this->error .= "wrong email or password<br>";
		}

		return $this->error;
		
	}

	private function hash_text($text){

		$text = hash("sha1", $text);
		return $text;
	}

	public function check_login($id,$redirect = true)
	{
		if(is_numeric($id))
		{

			$query = "select * from users where userid = '$id' limit 1 ";

			$DB = new Database();
			$result = $DB->read($query);

			if($result)
			{

				$user_data = $result[0];
				return $user_data;
			}else
			{
				if($redirect){
					header("Location: ".ROOT."login");
					die;
				}else{

					$_SESSION['mybook_userid'] = 0;
				}
			}
 
			 
		}else
		{
			if($redirect){
				header("Location: ".ROOT."login");
				die;
			}else{
				$_SESSION['mybook_userid'] = 0;
			}
		}

	}
 
}