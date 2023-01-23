<?php 
require("time.php");
$time_class = new Time();
echo $time_class->get_time(date("Y-m-d H:i:s"));