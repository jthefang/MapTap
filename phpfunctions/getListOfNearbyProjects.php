<?php
/*
	will be called by "js/mainIndexJS.js"
	gets the story information of the nearby stories within a defined radius of the user's location
		(sent as userLat and userLng) in the GET
		will be displayed when the page loads on "index.php" and IF the user has allowed the page to user his/her's location
*/
	include("mainfunctions.php"); //connects to database

function print_array($array) {
	// Print a nicely formatted array representation (for debugging data)
  	echo '<pre>';
 	print_r($array);
 	echo '</pre>';
}


//Vincenty formula for distance on earth from 2 points
function getDistance($latitude1, $longitude1, $latitude2, $longitude2) {
	$earth_radius = 6371;
	
	$dLat = deg2rad($latitude2 - $latitude1);
	$dLon = deg2rad($longitude2 - $longitude1);
	
	$a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
	$c = 2 * asin(sqrt($a));
	$d = $earth_radius * $c;
	
	return $d;
}

	if(isset($_GET['userLat'])){ //ajax request was submitted
		//Define query for an user-selected poll
		$latitude = $_GET['userLat'];
		$longitude = $_GET['userLng'];
		/*
			location_lat and location_lng are columns in the "stories" table
			3959 is miles of the radius at the equator using the "Vincenty formula"
			"HAVING distance < 25" : 25 is mile radius
		*/
																						//keep this here comma at the end
        $query = "SELECT story_id, user_id, story_headline, story_text, story_datetime, location_lat, location_lng, 
			(
			    3959 * acos(
			      	cos(
			        	radians({$latitude})
			      	) * cos(
			        	radians(location_lat) 
			      	) * cos(
			        	radians(location_lng) - radians({$longitude})
			      	) + sin(
			        	radians({$latitude})
			      	) * sin(
			        	radians(location_lat)
			      	)
			    )
			) AS distance
			FROM stories
			WHERE hasExpired=0
			HAVING distance < 25
			ORDER BY distance ASC
			LIMIT 0, 5"; 
		if ($result = @mysql_query($query, $dbc)) {
			if (mysql_num_rows($result)==0){ 
				echo null; //send null as data 
				exit(); //end script
			}
	        $storyInfoArray = array(); //multidimensional array
	        while ($row = mysql_fetch_array($result)) {
	        	/*if (isset($row['tags'])) { //If tags column is set for this story
	                $tags = $row['tags']; //and add to array
	            }*/
	            //format the description
	            $storyDescrip = $row['story_text'];
	            if(strlen($storyDescrip) > 145){
	            	$cutoff = strrpos(substr($row['story_text'], 0, 145), ' '); //find the end of the last word
	            	$storyDescrip = substr($row['story_text'], 0, $cutoff) . "...";
	            }

	            //get the proximity of story
	            $proximity = round(getDistance($row['location_lat'], $row['location_lng'], $latitude, $longitude), 1);

	            //format datetime
	            $dt = date_create($row['story_datetime']);
				$date = date_format($dt, 'D M d, Y'); //Thu Jan 21, 2015

	            $storyInfoArray[] = array("story_id" => "{$row['story_id']}",
	            	"user_id" => "{$row['user_id']}",
	        		"story_headline" => "{$row['story_headline']}",
	        		"story_text" => "{$storyDescrip}",
	        		"story_date" => "{$date}",
	        		"proximity_to_user" => "{$proximity}"
	        	);
	        } //End of while loop
	    } else { //Query didn't run
	        print '<p style="border: red; color: red;">Error, something occurred which prevented the query from executing. ' 
	        	. mysql_error($dbc) . '</p>';
	    }

	    /*encoded like:
	    {
			"story_id" : story_id,
			"user_id" : user_id,
    		"story_headline" : story_headline,
    		"story_text" : story_text,
    		"story_date" => date,
    		"proximity_to_user" => proximity of story (calculated by php script)
	    }
	    */
	    echo json_encode($storyInfoArray); 
	    //print_array($storyInfoArray);
	}
?>