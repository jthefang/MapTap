<?php 
include("phpfunctions/mainfunctions.php"); 
session_start(); //for facebook login (set up in "header.php")

require_once "recaptcha.php";
// your secret key
$secret = "6LfbGA8TAAAAAKXY8ocGNPyYVSRUx2IGrF7ZnXYp";
$response = null;
$reCaptcha = new ReCaptcha($secret);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Login or Signup</title>
    
      <!-- style stuff -->
      <link type="text/css" rel='stylesheet' href='css/mystyles/mainLoginStyle.css' /> <!--this page's style stuff-->
      <link href="css/jquery-ui.min.css" type="text/css" rel="stylesheet" /><!--jQuery UI style-->
      
      <!-- JS and jQuery stuff -->
      <script src="http://code.jquery.com/jquery-latest.min.js"></script> <!--jQuery Library-->
      <script type="text/javascript" src="js/jquery-ui.min.js"></script> <!--jQuery UI-->
      <!--*********************- include JS file for index page *********************** -->
      <script src="js/myscripts/mainLoginJS.js"></script> <!--main JS for this page-->

      <!--tab icon-->
      <link rel="shortcut icon" href="images/world_map.ico">
      
      <!--the sign in recaptcha-->
      <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body>
        <div id="container">
          <?php include("templates/header.php"); ?>
      <!--***************************-Beginning Page-******************************-->

        <div id="page_content">
          <div id="tabs">
            <ul class="logmod__tabs">
              <li data-tabtar="lgm-1"><a href="#login">Login</a></li>
              <li data-tabtar="lgm-2"><a href="#signup">Sign Up</a></li>
            </ul>
            <div class="logmod__tab-wrapper">
              <div id="login" class="ls_fxn logmod__tab lgm-1">
                <div class="wrap">
                  <div class="avatar">
                    <img src="images/world_map.png">
                  </div>
                  
                  <?php
                    echo "<div id='login_err' class='login err_msg'>Please enter your credentials</div>"
                  ?>
                  <input type="text" id="login_email" name="login_email" placeholder="email address" class="login" maxlength="240" required>
                  <div class="bar">
                    <i></i>
                  </div>
                  
                  <input type="password" id="login_password" name="login_password" placeholder="password" class="login" maxlength="30" required>
                  <!--<a href="" id="forgot_link">forgot ?</a>-->
                  
                  <button id="login" class="sign_btn">Login</button>
                  <!--<div class="logmod__alter">
                    <div class="logmod__alter-container">
                      <a href="#" class="connect facebook">
                        <div class="connect__icon">
                          <i class="fa fa-facebook"></i>
                        </div>
                        <div class="connect__context">
                          <span>Sign in with <strong>Facebook</strong></span>
                        </div>
                      </a>
                      <a href="#" class="connect googleplus">
                        <div class="connect__icon">
                          <i class="fa fa-google-plus"></i>
                        </div>
                        <div class="connect__context">
                          <span>Sign in with <strong>Google+</strong></span>
                        </div>
                      </a>
                    </div>
                  </div>-->
                </div>
              </div>
              <div id="signup" class="ls_fxn logmod__tab lgm-2">
                <div class="wrap">
                  <div class="avatar">
                    <img src="images/world_map.png">
                  </div>
                  
                  <?php
                    echo "<div id='signup_err' class='login err_msg'>Fill in the form below to sign up</div>"
                  ?>
                  <input type="text" id="signup_email" name="signup_email" placeholder="Enter your email address" class="login" maxlength="240" required>
                  <div class="bar">
                    <i></i>
                  </div>
                  <input type="password" id="signup_password" name="signup_password" placeholder="Enter a password" class="login" maxlength="30" required>
                  <div class="bar">
                    <i></i>
                  </div>
                  <input type="password" id="confirm_password" name="confirm_password" placeholder="Reenter your password" class="login" maxlength="30" required>
                  <div class="bar">
                    <i></i>
                  </div>
                  <input type="text" id="signup_firstname" name="signup_firstname" placeholder="First name" class="login names" maxlength="30" required>
                  <input type="text" id="signup_lastname" name="signup_firstname" placeholder="Last name (optional)" class="login names" maxlength="30" required>
                  

                  <div class="g-recaptcha" data-sitekey="6LfbGA8TAAAAAN4iQ-WSFpYBZX3PctuqxQgniNQn"></div>
                  <button id="signup" class="sign_btn">Sign up</button>
                  <!--<div class="logmod__alter">
                    <div class="logmod__alter-container">
                      <a href="#" class="connect facebook">
                        <div class="connect__icon">
                          <i class="fa fa-facebook"></i>
                        </div>
                        <div class="connect__context">
                          <span>Sign up with <strong>Facebook</strong></span>
                        </div>
                      </a>
                      <a href="#" class="connect googleplus">
                        <div class="connect__icon">
                          <i class="fa fa-google-plus"></i>
                        </div>
                        <div class="connect__context">
                          <span>Sign up with <strong>Google+</strong></span>
                        </div>
                      </a>
                    </div>
                  </div>-->
                </div>
              </div>
            </div>
          </div>
        </div>
            <?php // if submitted check response
              if (!empty($_POST)){ //Check if form is submitted
                if ($_POST["g-recaptcha-response"]) {
                    $response = $reCaptcha->verifyResponse(
                        $_SERVER["REMOTE_ADDR"],
                        $_POST["g-recaptcha-response"]
                    );
                } 
                if ($response != null && $response->success) {
                  //handle form
                } else {
                }
              } //end if !empty $_POST
            ?>
<?php include("templates/footer.php"); ?>