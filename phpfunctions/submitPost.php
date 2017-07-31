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

	if (!empty($_POST)){
		//Define query 
		$authorId = $_POST['author_id'];
		$storyId = $_POST['story_id'];
		$content = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['post_content']))));;

        $query = "INSERT INTO story_posts(author_id, story_id, post_content) 
            VALUES('$authorId', '$storyId', '$content')"; //default date_submitted is current timestamp in db
        @mysql_query($query, $dbc);
        $successArray = array();
        if (mysql_affected_rows($dbc) == 1) { //something changed
            $successArray["success"] = true;
        } else {
            $successArray["success"] = false;
        }
        
	    /*encoded like:
	    {
	    	"success" : "true"
	    }
	    */
	    echo json_encode($successArray); 
	    //print_array($successArray);
	} //end if(isset($_POST))
?>