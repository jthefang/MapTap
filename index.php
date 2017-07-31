<?php 
include("phpfunctions/mainfunctions.php"); 
session_start(); //for facebook login (set up in "header.php")
?>

<!DOCTYPE html>
<html>
    <head>
        <title>MapTap</title>
    
    	<!-- style stuff -->
        <link type="text/css" rel='stylesheet' href='css/mystyles/mainIndexStyle.css' /> <!--this page's style stuff-->
        <link href="css/jquery-ui.min.css" type="text/css" rel="stylesheet" /><!--jQuery UI style-->
    	
    	<!-- JS and jQuery stuff -->
    	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script> <!--jQuery Library-->
        <script type="text/javascript" src="js/jquery-ui.min.js"></script> <!--jQuery UI-->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB49s0bp_zuZwDFzmT17aqjZHmOtY13jJI&libraries=places"></script> <!--google maps places (for autocomplete)-->
        <script src="js/oms.min.js"></script> <!--OverlappingMarkerSpiderfier library-->
		<script src="js/myscripts/mainIndexJS.js"></script> <!--main JS for this page-->

        <!--Google analytics-->
<script>
    (function(i,s,o,g,r,a,m) {
        i['GoogleAnalyticsObject']=r;
        i[r]=i[r]||function() {
            (i[r].q=i[r].q||[]).push(arguments)
        }, i[r].l=1*new Date();
        a=s.createElement(o),m=s.getElementsByTagName(o)[0];
        a.async=1;
        a.src=g;
        m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-60035794-2', 'auto');
    ga('send', 'pageview');
</script>
        <!--tab icon-->
        <link rel="shortcut icon" href="images/tab_icon.ico">
    </head>
    <body>
        <div id="container">
        	<?php include("templates/header.php"); ?>

            <div id="page_content">
    			<div id="map_canvas"></div>
    			<!--see "js/myscripts/mainIndexJS.js" will display a custom message 
    				to this div if user clicks on a marker-->
    			<div id="map_message" style="display:none;"></div>
                <input type="text" name="location_search" id="location_search" class="location_search_controls" placeholder="Enter a location" style="display: none;">
            </div>
            
<?php include("templates/footer.php"); ?>
