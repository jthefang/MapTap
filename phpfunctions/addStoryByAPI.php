<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes, to let geoloc do its thing, take its time (see code below for sleep())
/*
	will be called by "js/mainIndexJS.js"
	inputs stories from API onto server
*/
include("mainfunctions.php"); //connects to database

function print_array($array) {
	// Print a nicely formatted array representation (for debugging data)
  	echo '<pre>';
  	print_r($array);
  	echo '</pre>';
}

class geocoder{
    static private $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=";

    static public function getLocType($address){
    	$url = self::$url.urlencode($address);
        
        $resp_json = self::curl_file_get_contents($url);
        $resp = json_decode($resp_json, true);

        if($resp['status']='OK'){
        	if(isset($resp['results'][0]))
	            return $resp['results'][0]['types'][0];
        }else{
            return false;
        }
    }

    static public function getLocation($address){
    	$url = self::$url.urlencode($address);
        $resp_json = self::curl_file_get_contents($url);
        $resp = json_decode($resp_json, true);
    	if($resp['status']='OK'){
    		if(isset($resp['results'][0]))
            	return $resp['results'][0]['geometry']['location'];
        }else{
            return false;
        }
    }

    static public function getFormatted($address){
    	$url = self::$url.urlencode($address);
        
        $resp_json = self::curl_file_get_contents($url);
        $resp = json_decode($resp_json, true);

        if($resp['status']='OK'){
        	if(isset($resp['results'][0]))
            	return $resp['results'][0]['formatted_address'];
        }else{
            return false;
        }
    }

    static private function curl_file_get_contents($URL){
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
            else return FALSE;
    }
}

	if (!empty($_POST)){
		//Define query 
		/*
		"title"			: title,
    	"date_published": datepublished,
    	"author"		: author,
    	"abstract"		: abstract,
    	"geoloc"		: geoloc,
    	"url"			: url 
		*/ 
		$title = $_POST['title'];
		$date_published = $_POST['date_published'];
			$date = strtotime($date_published);
		$author = $_POST['author'];
		$abstract = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['abstract']))));;
		$url = $_POST['url'];
		$img_url = $_POST['img_url'];

		//in order to avoid double posting
		$query = "SELECT * 
	    	FROM stories 
	    	WHERE author_name = '{$author}' 
	    		AND url_api = '{$url}'"; 
	    if ($result = @mysql_query($query, $dbc)) {
	    	if(mysql_num_rows($result) != 0){ //this article has already been posted
	    		$successArray["success"] = false;
	    		$successArray["error"] = "This story has already been submitted.";
	    	}else{
				$loc = $_POST['geoloc'];
				$lat = $lng = $formatted_address = false;
				if($loc != ""){
					if(strpos($loc, ",")){ //there's a comma
		        		$locs = explode(',', $loc); //create an array of locations delimited by comma
		        		$types = array(
		        			"route",
		        			"locality",
		        			"administrative_area_level_1",
		        			"country"
		        		);
		        		$match = false; //keep track of whether a loc type is found, needed in order to cycle through locs array many times to pref certain loc types more (ie. 1: locality 2: administrative_area_level_1 3:country)
		        		$i_type = 0;
		        		do{
		        			$i = 0; //counter to track for last element in foreach of locs;
		        			$length = count($locs);

		        			foreach($locs as $value){
			        			$address = urlencode($value);
			        			if(geocoder::getLocType($address) == $types[$i_type]){
			        				$location = geocoder::getLocation($address);
			        				$formatted_address = geocoder::getFormatted($address);
			        				$match = true;
			        				break;
			        			}//end if loctype = locality 

			        			if($i == $length - 1){ //if last element and no loc type then move to next loc type and start over
			        				$i_type++;
			        			}

			        			$i++;
			        			sleep(10); //give geocoder a rest so no over_query_limit error
			        		}//end foreach locs 	
		        		} while(!$match); //end do while no loc types match        		
		        	} else{
		        		$location = geocoder::getLocation($loc);
						$formatted_address = geocoder::getFormatted($loc);
		        	}
					$lat = $location['lat'];
					$lng = $location['lng'];
					/*echo "location array: ";
					print_r($location); //////////////////debug
					echo "<br/>formated address: " . $formatted_address;	 ////////////////////////debug* /
					
					$locs = explode(',', $loc); //create an array of locations delimited by comma
					echo "<br/>locations delimited: ";
					print_r($locs);
					foreach($locs as $value){
						$one = urlencode($value);
						echo "<br/>urlencoded: " .  $one;
						echo "<br/>types: " . geocoder::getLocType($one);
					}/////////////////////////////////////debug*/
				} else{
					$formatted_address = $lat = $lng = null;
				} //end not empty $_POST['geoloc']
				//echo "<br/>title: " . $title . "<br/>date: {$date}<br/>author: {$author}<br/>abstract: {$abstract}<br/>url: " . $url . "<br/>lat= " . $lat . "\tlng = " . $lng . "<br/>"; //////////////debug

				//wait for geoloc to return	
				sleep(20); 
				
		        $query = "INSERT INTO stories(story_headline, story_datetime, author_name, story_text, url_api, location_lat, location_lng, location_formatted_address, img_url) 
		            VALUES('$title', FROM_UNIXTIME($date), '$author', '$abstract', '$url', '$lat', '$lng', '$formatted_address', '$img_url')"; 
		        @mysql_query($query, $dbc);
		        $successArray = array();
		        if (mysql_affected_rows($dbc) == 1) { //something changed
		            $successArray["success"] = true;
		            $successArray["error"] = "none";
		        } else {
		            $successArray["success"] = false;
		            $successArray["error"] = "Not able to insert.";
		        }//end story insert success*/
	    	}//end if story already exists
	    }//end SELECT query call

        
	    /*encoded like:
	    {
	    	"success"  	: "true"
	    	"error"		: "some error"
	    }
	    */
	    
	    echo json_encode($successArray); 
	    //print_array($successArray); //////////////////////////////debugging
	    sleep(1);
	} //end if(isset($_POST))
?>