<?php
/*
	will be called by "js/mainLoginJS.js"
*/
include("mainfunctions.php"); //connects to database

function print_array($array) {
// Print a nicely formatted array representation (for debugging data)
  echo '<pre>';
  print_r($array);
  echo '</pre>';
}

if(isset($_GET['email']) && isset($_GET['password']) && isset($_GET['firstname']) ){
	//Define query 
	$email = $_GET['email'];
	$password = $_GET['password'];
	$firstName = $_GET['firstname'];
	$lastName = $_GET['lastname'];

	$successArray = array();
	// check if e-mail address syntax is valid or not
	$email = filter_var($email, FILTER_SANITIZE_EMAIL); // sanitizing email(Remove unexpected symbol like <,>,?,#,!, etc.)
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$successArray["success"] = false;
		$successArray["error"] = "Invalid email address";
	}else{
		$query = "SELECT * 
	    	FROM users 
	    	WHERE email = '{$_GET['email']}' 
	    		AND password = '{$_GET['password']}'"; 
	    if ($result = @mysql_query($query, $dbc)) {
	    	if(mysql_num_rows($result)!=0){ //this user is already registered
	    		$successArray["success"] = false;
	    		$successArray["error"] = "This user is already registered";
	    	}else{
	    		$query = "INSERT INTO users(email, password, first_name, last_name) 
			        VALUES('$email', '$password', '$firstName', '$lastName')";
			    @mysql_query($query, $dbc);
			    if (mysql_affected_rows($dbc) == 1) { //something changed
			        $successArray["success"] = true;
			        $successArray["error"] = "none";
			    } else {
			        $successArray["success"] = false;
			        $successArray["error"] = "Oops, something went wrong";
			    }
	    	}
	    }
	} 

    /*encoded like:
	    {
			"success" : success of signing up = some boolean
	    }
    */
    echo json_encode($successArray); 
    //print_array($userInfoArray);
}
//mysql_close ($connection); // Connection Closed.
?>