<?php
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include("$root/MapTap/templates/local_vars.php"); //include("$root/templates/live_site_vars.php");
$dbc = mysql_connect($hostname, $user, $password);
mysql_select_db($dbname, $dbc);
global $dbc;

/**
    returns the time elapsed since $time
*/
function humanTiming($time){
    $time = time() - $time; // to get the time since that moment

    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits>1) ? 's' : ''); //returns time since now (ie. 1 day, 2 hours). Be sure to append " ago"
    }
}

function executeQuery($query, $success_message = 'none', $display_error_message = true) {
    global $dbc;
    @mysql_query($query, $dbc);

    if($success_message != 'none'){ //function called with a message
        if (mysql_affected_rows($dbc) == 1) { //something changed
            print "<p style='border: green; color: green;'><img src='images/check_icon.png' id='check_icon' style='width: 1em; height: 1em'/> {$success_message}</p>";
        } else {
            if($display_error_message){ 
                print '<p style="border: red; color: red;">Error, something occurred which prevented the action from executing. ' . mysql_error($dbc) . '</p>';
            }
        }
    }
}
?>