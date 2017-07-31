<?php
$wepayId = "116276";
define("WEPAY_CLIENT_ID", $wepayId);
define("WEPAY_CLIENT_SECRET", "50f678683c");

require 'wepay_sdk.php';
Wepay::useStaging(WEPAY_CLIENT_ID, WEPAY_CLIENT_SECRET); //client_id and client_secret ***************************CHANGE TO useProduction when going live**************************
session_start();
?>