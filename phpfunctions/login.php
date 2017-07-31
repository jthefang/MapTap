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

if(isset($_GET['email']) && isset($_GET['password'])){
	//Define query 
	$email = $_GET['email'];

	// check if e-mail address syntax is valid or not
	$email = filter_var($email, FILTER_SANITIZE_EMAIL); // sanitizing email(Remove unexpected symbol like <,>,?,#,!, etc.)
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$userInfoArray = array("error" => "Invalid email address");
	}

    $query = "SELECT * 
    	FROM users 
    	WHERE email = '{$_GET['email']}' 
    		AND password = '{$_GET['password']}'"; 
    if ($result = @mysql_query($query, $dbc)) {
    	if(mysql_num_rows($result)!=0){
    		$row = mysql_fetch_array($result);

	        $userInfoArray = array(
	        	"error" => "none",
	        	"redirectUrl" => "index.php",
	        	"first_name" => "{$row['first_name']}"
	        );

	        session_start();
			$_SESSION['user'] = array(
				'first_name' => $row['first_name'],
				'last_name' => $row['last_name'],
				'full_name' => $row['first_name'] . " " . $row['last_name'],
				'id' => $row['user_id'],
				'email' => $row['email'],
				'user_last_login' => $row['last_login'],
				'user_account_created' => $row['date_created']
			);

			//Define query (note that $userId is retrieved by header.php)
            $query = "UPDATE users
                SET last_login=now()
                WHERE user_id = {$row['user_id']}";
            executeQuery($query, "none", true); //false means don't display error message (if there is one)
    	}
    	else{
    		$userInfoArray = array("error" => "That email and password do not match");	
    	}
    } else { 
    	$userInfoArray = array("error" => "Oops, something happened to our servers");
    }

    /*encoded like:
	    {
			"error" : some error message,
			"redirectUrl" : redirect (only if query was sucessful, ie. error='none')
	    }
    */
    echo json_encode($userInfoArray); 
    //print_array($userInfoArray);
}

//mysql_close ($connection); // Connection Closed.
?>