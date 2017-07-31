<?php //the session for a page should be started on each individual page file at the top
?>

<!-- include mainJS that will be displayed on every page 
	(assuming header.php is included on every page too)-->            
<script src="js/myscripts/mainJS.js"></script> 
<script src="js/jquery.navbar.js"></script> <!--Navbar plugin for header(custom)-->
<script>
</script>

<!--header style stuff (same for every page)-->
<link type="text/css" rel='stylesheet' href='css/mystyles/mainStyle.css' />

<div id='header_bar'>
	<?php 
		$allowedPagesWithoutLogin = array( 
			$siteURLs["home"],
			$siteURLs["add_story"],
			$siteURLs["view_news"],
			$siteURLs["login"],
			$siteURLs["get_api_stories"],
			$siteURLs["about"]
		);
	     
	    if ((!isset( $session ) || $session === null ) && !(isset($_SESSION['user']))) {
		    // no session exists

	    	//redirect if not logged in and viewing an "illegal page" (see array $allowedPagesWithoutLogin above for "legal pages")
	    	$redirect = true;
		  	for($i = 0; $i < count($allowedPagesWithoutLogin); $i++){
		    	if($_SERVER['PHP_SELF'] == $allowedPagesWithoutLogin[$i]){
		    		$redirect = false;
		    	}
		    }
		    if($redirect){
			    /* Redirect browser to login page if not logged in and not on legal page*/
	    		header("Location: login.php"); /*###########################url############*/
				exit();
			}
	    }
	     
	    // see if we have a session
	    if (isset($session) || isset($_SESSION['user'])) {
	    	if($_SERVER['PHP_SELF'] == $siteURLs['login']){
	    		/* Redirect browser to home page if logged in and trying to access login page*/
	    		header("Location: index.php"); /*###########################url############*/
				exit();
	    	}

	      	/*********---------------display menu for logged in user----------------*********/
	      
	      	echo '<nav><ul id="navbar_menu">
	      			<!--put all on one line so that there is no spacing between horizontal list items-->
	      			<li><a href="index.php"><img src="images/world_map.png" class="navbar_icon"/><span class="navbar_link">MAP</span></a></li><!--<li><a href="add_story.php"><img src="images/publish_icon.png" class="navbar_icon"/><span class="navbar_link">PUBLISH</span></a></li>--><!--<li><a href="my_projects.php"><img src="images/my_projects_icon.png" class="navbar_icon"/><span class="navbar_link">YOUR STORIES</span></a></li>--><li><a href="about.php"><img src="images/about_icon.png" class="navbar_icon"/><span class="navbar_link">ABOUT</span></a></li>
	      		</ul></nav>';
	      	// print logout url using session and redirect_uri (logout.php page should destroy the session)
      		echo '<div id="fb_user">
      				<div id="notifications" class="fb_user_button">
      					<img src="images/notifications_icon.png" id="notifications_icon_image" class="img_button"/>
      					<div id="notifications_div">
      						<ul class="user_list">
      							<li id="no_notifications" class="user_list_item"><a class="user_list_item_link">No new notifications</a></li>
      							<!--<li class="user_list_item action"><a class="user_list_item_link action"></a></li>-->
      						</ul>
      					</div>
      				</div>
      				<div id="user_profile" class="fb_user_button">
	      				<a class="fb_fxn_link">';
	      			if(isset( $_SESSION['fb_token'])){
	      				echo'<img src = "https://graph.facebook.com/'. $userId . '/picture?type=square&height=18&width=18" id="fb_propic"/>
	      					<span class="fb_title">' . $user->getFirstName() . '</span>';
			      	} else if(isset($_SESSION['user'])){ //logged in w/ TreeBoks 
			      		echo'<img src = "images/profile_icon.png" height="18" width="18" id="fb_propic"/>
	      					<span class="fb_title">' . $_SESSION['user']['first_name'] . '</span>';
			      	}
				      	echo '<img src="images/down_icon.png" id="show_more_icon" height="20" width="20" style="vertical-align: middle" />
	      					<img src="images/down_icon_selected.png" id="show_more_icon_selected" height="20" width="20" style="vertical-align: middle; display: none" />
	      				</a>
	      				<div id="user_options_menu_div">
	      					<ul class="user_list">
	      						<li class="user_list_item action">';	
      				if(isset($_SESSION['user'])){ //logged in w/ TreeBoks 
      							echo'<a href="logout.php" class="user_list_item_link action"><span>Logout</span></a>';
      				}
							echo'</li>
		      				</ul>
	      				</div>
	      			</div>
	      		</div>'; /*---------------the propic is linked to my_projects.php for now-----------------*/
	  	} else { //session does not exist
	      	// show login url
	  		/*echo '<nav><ul id="navbar_menu">
	      			<li><a href="index.php"><img src="images/world_map.png" class="navbar_icon"/><span class="navbar_link">MAP</span></a></li><li><a href="about.php"><img src="images/about_icon.png" class="navbar_icon"/><span class="navbar_link">ABOUT</span></a></li>
	      		</ul></nav>
	      		<!--###############REMOVE THIS COMMENT TO SEE LOGIN BUTT<div class="block_login">
	  				<a href="login.php" id="signin_btn" class="buttonTwo">Login/Register</a>
	  				<!--<div class="btn-fb-button">
	  					<a href="' //. $helper->getLoginUrl( array( 'email', 'user_friends' )) 
	  						. '"><span class="icon"></span>
	  						<span class="title">Login with Facebook</span></a>
	  				</div>--
	  			</div>-->';*/
	  	}// end if(isset(session))
	?>
</div> <!--end header_bar div-->


