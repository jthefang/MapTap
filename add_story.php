<?php 
include("phpfunctions/mainfunctions.php"); //if you need to call database include mainfunctions
session_start(); //for facebook login (set up in "header.php")
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Publish a story for the world.</title>
    
    	<!-- style stuff -->
        <link type="text/css" rel='stylesheet' href='css/mystyles/mainAddStoryStyle.css' /><!--add_story page's style stuff-->
        <link href="css/jquery-ui.min.css" type="text/css" rel="stylesheet"> <!--jQuery UI style-->

        
    	<!-- JS and jQuery stuff -->
    	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script> <!--jQuery Library-->
        <script src="js/jquery.validate.min.js"></script> <!--Form validation-->
        <script type="text/javascript" src="js/jquery-ui.min.js"></script> <!--jQuery UI-->
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places"></script> <!--google maps places (for autocomplete and location verification)-->
    	<script src="js/myscripts/mainAddStoryJS.js"></script> <!--******************************** include JS for add_story *************************************-->

    	<!--tab icon-->
        <link rel="shortcut icon" href="images/tab_icon.ico">
    </head>
    <body>
        <div id="container">
        	<?php include("templates/header.php");?>
<!--***********************start content**********************-->
<div id='page_content'>
	<div id="add_story">
		<?php
		$storySubmitted = false;
		if (!empty($_POST)){ //Check if story_form.php is submitted
			//debugging $storyLocLat = $_POST['hidden_loc_address']; print $storyLocLat;}
			//Validate form
		    $problems = false;
			$error_message = "";
			$errorCounter = 0;

			function validateForm($form_element, $errorMessage){
				if (empty($form_element)) {
		        	$problems = true;
		        	$errorCounter++;
		        	return "<li class='error'>{$errorMessage}</li>";
		    	}
		    	return "";
			}
		    //Check if any required fields are empty
		    $error_message .= validateForm($_POST['story_topic'], "Please give your story a topic.");
		    $error_message .= validateForm($_POST['story_headline'], "Please give your story a headline.");
		    $error_message .= validateForm($_POST['story_text'], "Please provide a story to go with your headline.");
		    $error_message .= validateForm($_POST['story_loc'], "Please give your story a location.");
		    if ($_POST['hidden_loc_lat'] == 0) {
		        $problems = true;
		        $errorCounter++;
		        $error_message .= "<li class='error'>Please provide a valid location.</li>";
		   	}

		    /*/Validate date
		    $dateRegExp = '/^((((0[13578])|([13578])|(1[02]))[\/](([1-9])|([0-2][0-9])|(3[01])))|(((0[469])|([469])|(11))[\/](([1-9])|([0-2][0-9])|(30)))|((2|02)[\/](([1-9])|([0-2][0-9]))))[\/]\d{4}$|^\d{4}$/';
		    if (empty($_POST['story_date'])) {
		        $problems = true;
		        $error_message .= '<li>Please enter a date for your story.</li>';
		    } elseif (!preg_match($dateRegExp, $_POST['story_date'])) {
		    	$problems = true;
	    		$error_message .= '<li>Please enter a valid date for your story (mm/dd/yyyy).</li>';
			}*/

		    /*************************If no problems execute query*****************/
		    if (!$problems) {
		        //Set INSERT query variables
		        $storyTopic = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['story_topic']))));
		        $storyName = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['story_headline']))));
		        $storyContent = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['story_text']))));

		        $userId = null;
		        if(isset($_SESSION['user']['id'])){
		        	$userId = $_SESSION['user']['id'];
		        }
		        
		        //Get the location values
		        $storyLocLat = $_POST['hidden_loc_lat'];
		        $storyLocLng = $_POST['hidden_loc_lng'];
		        $storyLocAddress = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['hidden_loc_address']))));
		        $storyLocCity = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['hidden_loc_city']))));
		        $storyLocState = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['hidden_loc_state']))));
		        $storyLocZip = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['hidden_loc_zip']))));
		        $storyLocCountry = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['hidden_loc_country']))));
		        $storyLocFormattedAddress = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['hidden_loc_formatted_address']))));
		        
		        /*/Format the Time
				$storyTimeMin = "";
				if($_POST['select_minute'] == 0){
					$storyTimeMin = "0" . $_POST['select_minute'];
				} else
					$storyTimeMin = $_POST['select_minute'];
		        $storyTime = $_POST['select_hour'] . ':' . $storyTimeMin . ' ' . $_POST['select_period'];

		        //Make a DateTime object (NOTE that the time is also stored separately)
		        $storyDate = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['story_date']))));
		        $storyDate = new DateTime($storyDate . ' ' . $storyTime); //convert to date time formatting
		        $storyDate = $storyDate->format('Y-m-d H:i'); //convert back to string (trust me this is necessary, mysql will not do it for you)*/
		        
		        //Define query (note that $userId is retrieved by header.php)
		        $query = "INSERT INTO stories(story_topic, story_headline, user_id, story_text, 
		        		location_lat, location_lng, location_address, location_city, location_state, location_zipcode, 
		        		location_country, location_formatted_address) 
		            VALUES('$storyTopic', '$storyName', '$userId', '$storyContent', 
		            	'$storyLocLat', '$storyLocLng', '$storyLocAddress', '$storyLocCity', '$storyLocState', '$storyLocZip', 
		            	'$storyLocCountry', '$storyLocFormattedAddress')";
		        executeQuery($query, "Story added to the map");

		        //$storyId = mysql_insert_id();

		        //for sticky form to clear form
		        $storySubmitted = true;
		    } elseif ($problems) { //Fields are empty or incorrect
		    	$pluralErrors = "";
		    	if($errorCounter == 1){
		    		$pluralErrors = " is 1 error";
		    	} else
		    		$pluralErrors = " are {$errorCounter} errors";
		        print "<p class='error'>Please make sure you filled out the entire form correctly. There {$pluralErrors}.</p>
		        	<ul class='ul_error'>{$error_message}</ul><br/>";
		    } //end of query if
		}//End of form submit if*/

		$story_form_action = "add_story.php";
		$story_form_id = "add_story_form";
		$story_form_legend = "Publish a story for the world.";
		$story_form_element_class = "add_story";
		$story_form_submit_button_value = "Publish";
		include("templates/story_form.php"); 
		?>
	</div><!--*********End add_story div***********-->
</div>

<?php include("templates/footer.php"); ?>