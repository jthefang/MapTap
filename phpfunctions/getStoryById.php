<?php
/*
	will be called by "js/mainIndexJS.js"
	gets all the story information of the story with the sent id in the GET
		will be displayed when the user clicks on a marker on the map on "index.php"
*/
	include("mainfunctions.php"); //connects to database

function print_array($array) {
// Print a nicely formatted array representation (for debugging data)
  echo '<pre>';
  print_r($array);
  echo '</pre>';
}
	
	if(isset($_GET['getstoryId'])){
		//Define query for an user-selected poll
		/*$query = "SELECT location_lat, location_lng  FROM stories 
		WHERE (lat and lng) - (location_lat and lng) < .01  LIMIT 100"; //limit markers by location and number*/
        $query = "SELECT * FROM stories 
        	WHERE story_id = {$_GET['getstoryId']} LIMIT 1"; 
        if ($result = @mysql_query($query, $dbc)) {
            $row = mysql_fetch_array($result);
			
			//format the description
            $storyAbstract = html_entity_decode($row['story_text']);
            if(strlen($storyAbstract) > 230){
            	$abridged = substr($row['story_text'], 0, 230);
            	$cutoff = strrpos($abridged, ' '); //find the end of the last word
	            $storyAbstract = substr($row['story_text'], 0, $cutoff) . "...";
            }

			$dt = date_create($row['story_datetime']);
			$date = date_format($dt, 'l F jS, Y \a\t g:i A');
			//ie. g:ia \o\n l jS F Y
			//output = 5:45pm on Saturday 24th March 2012

			$now = date('m/d/Y h:i:s a', time()); //current date time
			$secondsUntilstory = strtotime($row['story_datetime']) - strtotime($now); //difference in seconds
			$daysUntilstory = round($secondsUntilstory / 86400, 0); //number of days
			$hoursUntilstory = round($secondsUntilstory / 3600, 0);


            /*if (isset($row['tags'])) { //If tags column is set for this story
                $tags = $row['tags']; //and add to array
            }*/
            $storyInfoArray = array("story_id" => "{$row['story_id']}",
            	"user_id" => "{$row['user_id']}",
        		"story_headline" => "{$row['story_headline']}",
        		"story_text" => $storyAbstract,
        		"story_date" => "{$date}",
        		"story_address" => "{$row['location_formatted_address']}",
        		"lat" => "{$row['location_lat']}",
        		"lng" => "{$row['location_lng']}",
        		"url" => "{$row['url_api']}",
        		"img_url" => "{$row['img_url']}");
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
    		"story_date" : story_date (formatted),
    		"story_address" : story_address (formatted), 
			"lat" : location_lat,
			"lng" : location_lng,
			"url" : url_api,
			"img_url" : img_url
	    }
	    */
	    echo json_encode($storyInfoArray); 
	    //print_array($storyInfoArray);
	}
?>