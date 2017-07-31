<?php
//echo $_SERVER['PHP_SELF'];
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
echo "<br>" . $root;

echo "<br/><img src='http://www.personal.psu.edu/jul229/mini.jpg'/>";
/*session_start(); //for facebook login (set up in "header.php")
$timezone = @$_SESSION['timezone']; //set with phpfunctions/timezone.php in the jQuery below
?>

<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script> <!--jQuery Library-->
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places"></script> <!--google maps places (includes autocomplete)-->
<script>
	$(document).ready(function() {
		var timezone = "";
        if("<?php echo $timezone; ?>".length==0){
	        if (navigator.geolocation) {
	            navigator.geolocation.getCurrentPosition(function (position) {
	                userLoc = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
		            $.ajax({
		                type: "GET",
		                url: "https://maps.googleapis.com/maps/api/timezone/json?location="+position.coords.latitude+","+position.coords.longitude+"&timestamp="+(Math.round((new Date().getTime())/1000)).toString()+"&sensor=false",
		                success: function(data){
		                	timezone = data.timeZoneId;
		                	$.ajax({
				                type: "GET",
				                url: "phpfunctions/timezone.php",
				                data: 'time='+ timezone,
				                success: function(){
				                    location.reload();
				                }
				            }); //end ajax call to phpfunctions/timezone.php
		                } //end success
		            }); //end ajax
		        }); //end getCurrentPosition
		    } //end if geolocation 
        }
    });
</script>
<?php
	$projectDate = new DateTime("2014-11-20 20:15:00");
	$projectDate = $projectDate->format('a');
	echo "project date " . $projectDate . "<br/>";

	$projectDate = strtotime($projectDate);

	$now = new DateTime();
	//$expiryDate = $now->add(new DateInterval('P1D')); //The project will officially expire 1 day from its datetime
	$expiryDate = $now->format('Y-m-d H:i');
	echo $expiryDate . "</br><br/>";
	$expiryDate = strtotime($expiryDate);

	echo $projectDate - $expiryDate . "<br/>"; 
	echo round(($projectDate - $expiryDate) / 3600, 0) . "<br/><br/>";

	echo $expiryDate . ' ' . $projectDate;

	$now = date('m/d/Y h:i:s a', time()); //current date time
	echo "<br/>" . $now . " <br/>" . date_default_timezone_get() . "<br/>" . $timezone;

	$str = "hello";
	echo "<br/><br/>" . substr($str, strlen($str) - 1);
?>*/
?>