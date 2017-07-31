<?php 
include("phpfunctions/mainfunctions.php"); //connects to database
session_start(); //for facebook login (set up in "header.php")
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo "What's MapTap?"; ?></title>
    
    	<!-- style stuff -->
        <link type="text/css" rel='stylesheet' href='css/mystyles/mainAboutStyle.css' /> <!--this page's style stuff-->
        <link href="css/jquery-ui.min.css" type="text/css" rel="stylesheet" /><!--jQuery UI style-->
        <!-- fonts -->
        <link href='http://fonts.googleapis.com/css?family=Lato:900' rel='stylesheet' type='text/css'>
    	
    	<!-- JS and jQuery stuff -->
    	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script> <!--jQuery Library-->
        <script type="text/javascript" src="js/jquery-ui.min.js"></script> <!--jQuery UI-->
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places"></script> <!--google maps places (for autocomplete)-->
        <!--<script src="js/myscripts/mainAboutJS.js"></script> <!--*********************- include JS file for this page *********************** -->

        <!--tab icon-->
        <link rel="shortcut icon" href="images/tab_icon.ico">
    </head>
    <body>
        <div id="container"> <!--closed in footer-->
        	<?php include("templates/header.php"); ?>
			<!--***************************-Beginning Page-******************************-->
			
            <div id="page_content">
                <!--***********************start content**********************-->
                <div id="about_info">
            		<p>
                        MapTap is an open news source that uses a map interface to display the most relevant stories. <!--<b>Any user
                        can publish</b> a story, so long as the user is <a href="login.php">logged in</a>.-->
                    </p>
                    <p>
                        To view a news story on the home page, simply click on an <b>icon</b> to preview a story, and click on the headline to go to the full article (courtesy of <a href="http://www.nytimes.com/" target="_blank">The New York Times</a>). 
                    </p>
                </div> <!--end about_info div-->
            </div><!--end page_content div-->
<?php include("templates/footer.php"); ?>