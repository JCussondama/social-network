<?php 

class Signup
{

	private $error = "";

	public function evaluate($data)
	{

		foreach ($data as $key => $value) {
			# code...

			if(empty($value))
			{
				$this->error = $this->error . $key . " is empty!<br>";
			}

			if($key == "email")
			{
				if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$value)) {
        
 					$this->error = $this->error . "invalid email address!<br>";
    			}
			}

			if($key == "first_name")
			{
				if (is_numeric($value)) {
        
 					$this->error = $this->error . "first name cant be a number<br>";
    			}

    			if (strstr($value, " ")) {
        
 					$this->error = $this->error . "first name cant have spaces<br>";
    			}
 
			}

			if($key == "last_name")
			{
				if (is_numeric($value)) {
        
 					$this->error = $this->error . "last name cant be a number<br>";
    			}

    			if (strstr($value, " ")) {
        
 					$this->error = $this->error . "last name cant have spaces<br>";
    			}

			}
  
			
		}

		$DB = new Database();

		//check tag name
		$data['tag_name'] = strtolower($data['first_name'] . $data['last_name']);

		$sql = "select id from users where tag_name = '$data[tag_name]' limit 1";
		$check = $DB->read($sql);
		while(is_array($check)){

			$data['tag_name'] = strtolower($data['first_name'] . $data['last_name']) . rand(0,9999);
			$sql = "select id from users where tag_name = '$data[tag_name]' limit 1";
			$check = $DB->read($sql);
		}

		$data['userid'] = $this->create_userid();
		//check userid
		$sql = "select id from users where userid = '$data[userid]' limit 1";
		$check = $DB->read($sql);
		while(is_array($check)){

			$data['userid'] = $this->create_userid();
			$sql = "select id from users where userid = '$data[userid]' limit 1";
			$check = $DB->read($sql);
		}

		//check email
		$sql = "select id from users where email = '$data[email]' limit 1";
		$check = $DB->read($sql);
		if(is_array($check)){

			 $this->error = $this->error . "Another user is already using that email<br>";
		}
 

		if($this->error == "")
		{

			//no error
			$this->create_user($data);
		}else
		{
			return $this->error;
		}
	}

	public function create_user($data)
	{

		$first_name = ucfirst($data['first_name']);
		$last_name = ucfirst($data['last_name']);
		$gender = $data['gender'];
		$email = $data['email'];
		$password = $data['password'];
		$userid = $data['userid'];
		$tag_name = $data['tag_name'];
		$date = date("Y-m-d H:i:s");
		$type = "profile";

		$password = hash("sha1", $password);
		
		//create these
		$url_address = strtolower($first_name) . "." . strtolower($last_name);

		$query = "insert into users 
		(type,userid,first_name,last_name,gender,email,password,url_address,tag_name,date) 
		values 
		('$type','$userid','$first_name','$last_name','$gender','$email','$password','$url_address','$tag_name','$date')";


		$DB = new Database();
		$DB->save($query);
	}
 
	private function create_userid()
	{

		$length = rand(4,19);
		$number = "";
		for ($i=0; $i < $length; $i++) { 
			# code...
			$new_rand = rand(0,9);

			$number = $number . $new_rand;
		}

		return $number;
	}
}