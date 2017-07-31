<?php
/*
	will be called by "js/mainIndexJS.js"
	gets the first 100 markers from the server table "stories" and sends them
		back to be displayed on the map on "index.php"

	ALSO checks for expired stories and marks them as expired
*/
include("mainfunctions.php"); //connects to database

function print_array($array) {
// Print a nicely formatted array representation (for debugging data)
  echo '<pre>';
  print_r($array);
  echo '</pre>';
}

	if (isset($_POST['getmarkers'])) {
		$locBounds = "";
		if (isset($_POST['nwlat']) && isset($_POST['selat'])) { //the bounds of area to search for stories in
			$nwlat = $_POST['nwlat'];
			$nwlng = $_POST['nwlng'];
			$selat = $_POST['selat'];
			$selng = $_POST['selng'];
 			$locBounds = "AND location_lat <= {$nwlat} AND location_lat >= {$selat}
 				AND location_lng >= {$nwlng} AND location_lng <= {$selng}";
		}

		//get stories that have a location
        $query = "SELECT story_id, story_headline, location_lat, location_lng  
        	FROM stories 
        	WHERE TRIM(location_formatted_address) <> '' AND location_formatted_address IS NOT NULL {$locBounds}
        	ORDER BY story_datetime DESC 
        	LIMIT 30"; 
        if ($result = @mysql_query($query, $dbc)) {
	        $markerArray = array(); //multidimensional array
	        while ($row = mysql_fetch_array($result)) { //still results
	        	$markerArray[]= array("story_id" => "{$row['story_id']}",
	        		"story_headline" => "{$row['story_headline']}",
	        		"lat" => "{$row['location_lat']}",
	        		"lng" => "{$row['location_lng']}");
	        } //End of while loop
	    } else { //Query didn't run
	        print '<p style="border: red; color: red;">Error, something occurred which prevented the query from executing. ' 
	        	. mysql_error($dbc) . '</p>';
	    }

	    /*encoded like:
	    {
			"0": {
				"story_id" : story_id,
				"story_headline" : story_headline
				"lat" : location_lat,
				"lng" : location_lng
			}
			"1": {
				"story_id" : story_id,
				"story_headline" : story_headline
				"lat" : location_lat,
				"lng" : location_lng
			}
	    }
	    */
	    echo json_encode($markerArray); 
	    //print_array($markerArray);
	}
?>