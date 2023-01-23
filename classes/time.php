<?php 

Class Time {

	function get_time($pasttime , $today = 0, $differenceFormat = '%y' ){
 
		$today = date("Y-m-d H:i:s"); 
	    $datetime1 = date_create($pasttime);
	    $datetime2 = date_create($today);
	   
	    $interval = date_diff($datetime1, $datetime2);
 	    $answerY = $interval->format($differenceFormat);
		
		$differenceFormat = '%m';
	    $answerM = $interval->format($differenceFormat);
		
 		$differenceFormat = '%d';
	    $answer = $interval->format($differenceFormat);
  		
		$differenceFormat = '%h';
		$answer2 = $interval->format($differenceFormat);

		//check for how much time passed
			
			if ($answerY >= 1) {//one year has passed
			
				$answerY = date(" F jS, Y ",strtotime($pasttime));// . " at " . date("h:i:s a", strtotime($pasttime));
				return $answerY;
 				
			}else if ($answerM >= 1) {//one month has passed
			
				$answerM = date(" F jS, Y ",strtotime($pasttime));// . " at " . date("h:i:s a", strtotime($pasttime));
				return $answerM;
			
			}else if ($answer > 2) { //more than 2 days
 				
				$answer = date(" F jS, Y ",strtotime($pasttime));// . " at " . date("h:i:s a", strtotime($pasttime));
				return $answer;
				
			}else if ($answer == 2) { // 2 days
			
				return $answer . " d, " . $answer2 . " hr ago";// at " . date("h:i:s a", strtotime($pasttime));
				
 			}else if ($answer == 1) {// 1 day ago
				
				return "1 d, " . date("h:i:s a", strtotime($pasttime));
 							
			}else {// less than a day
				
				$differenceFormat = '%h';
				$answer = $interval->format($differenceFormat);
				
				$differenceFormat = '%i';
				$answer2 = $interval->format($differenceFormat);
				
					if (($answer < 24) && ($answer > 1)) {
						
						return $answer . " hr, " . $answer2 . " min ago";
						
					}else if ($answer == 1){
						
						return "an hour ago";
						
					}else {//less than an hour
					
						$differenceFormat = '%i';
						$answer = $interval->format($differenceFormat);
						
							if (($answer < 60) && ($answer > 1)) {
								
								return $answer . " minutes ago";
							
							}else if ($answer == 1) { 
							
								return "a minute ago";
								
							}else {
								
								$differenceFormat = '%s';
								$answer = $interval->format($differenceFormat);
								
									if (($answer < 60) &&( $answer > 10)) {
										
										return $answer . " seconds ago";
										
									}else if ($answer < 10) {
										
										return "few seconds ago";
										
									}
								
							}
							
					}
					
					
			}
									

	}

}