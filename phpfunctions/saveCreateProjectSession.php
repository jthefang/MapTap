<?php
session_start(); 

if (!empty($_POST)){
    /*$_SESSION['create_project_session'] = array(
    	"proj_name" => $_POST['proj_name']),
    	"proj_descript" => $_POST['proj_descript']),
		"proj_loc" => $_POST['proj_loc']),
		"proj_date" => $_POST['proj_date']),
		"proj_hour" => $_POST['proj_hour']),
		"proj_min" => $_POST['proj_min']),
		"proj_period" => $_POST['proj_period'])
    );*/
    $successArray = array();
    $successArray["success"] = true;
    echo json_encode($successArray);
} //end if(isset($_POST))
?>