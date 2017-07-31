<?php
/*
	will be called by "js/mainAddStoryJS.js"
	reads what user types into story topic input in Publish page and gives suggestions (autocomplete) based on server topics
*/
include("mainfunctions.php"); //connects to database

function print_array($array) {
// Print a nicely formatted array representation (for debugging data)
  echo '<pre>';
  print_r($array);
  echo '</pre>';
}

	$term = trim(strip_tags($_GET['term']));//retrieve the search term that autocomplete sends
	if(isset($term)){
		//Define query for an user-selected poll
        $query = "SELECT story_topic, COUNT(story_topic) topicOccurrence 
        	FROM stories 
        	WHERE story_topic LIKE '%$term%'
        	GROUP BY story_topic
    		ORDER BY topicOccurrence DESC
        	LIMIT 5"; 
        if ($result = @mysql_query($query, $dbc)) {
	        $topicArray = array(); //multidimensional array
	        while ($row = mysql_fetch_array($result)) { //still results
	        	$topicArray[]= "{$row['story_topic']}";
	        } //End of while loop
	    } else { //Query didn't run
	        print '<p style="border: red; color: red;">Error, something occurred which prevented the query from executing. ' 
	        	. mysql_error($dbc) . '</p>';
	    }

	    /*encoded like:
	    {
			"0": topic1,
			"1": topic2
			...
	    }
	    */
	    echo json_encode($topicArray); 
	    //print_array($topicArray);
	    //url format: http://www.mysite.com/mypage.html?var1=value1&var2=value2&var3=value3
	}
?>