<?php 
include("phpfunctions/mainfunctions.php"); //connects to database
session_start(); //for facebook login (set up in "header.php")

if(isset($_GET['proj_id'])){
    $projectId = $_GET['proj_id'];
    $query = "SELECT * FROM projects
            WHERE project_id = {$projectId}"; 
    if ($result = @mysql_query($query, $dbc)) { //successful query
        $row = mysql_fetch_array($result);

        $projectUserId = $row['user_id'];
        $projectName = $row['project_name'];
        //format the description
        $projectDescrip = $row['project_description'];

        /*project datetime stuff*/
        //format datetime
        $dt = date_create($row['project_datetime']);
        $date = date_format($dt, 'm/d/Y');
        $projectHasExpired = false;
        if($row['hasExpired'] == 1)
            $projectHasExpired = true;
        $projectStickyHour = date_format($dt, "g");
        $projectStickyMin = date_format($dt, "i");
        $projectStickyPeriod = date_format($dt, "A"); //AM or PM

        /*project location stuff*/
        $projectAddr = $row['loc_formatted_address'];
    } //end query success if
} else { //page accessed incorrectly, ie. without a  valid project_id (later redirect to an error page) /*#######################change this to error page##########################*/
    header("Location: index.php"); /*###########################this needs to be updated############*/
} // end isset($_GET['proj_id']) if
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo "Edit - " . $projectName; ?></title>
    
    	<!-- style stuff -->
        <link type="text/css" rel='stylesheet' href='css/mystyles/mainEditProjectStyle.css' /> <!--this page's style stuff-->
        <link href="css/jquery-ui.min.css" type="text/css" rel="stylesheet" /><!--jQuery UI style-->
        <!-- fonts -->
        <link href='http://fonts.googleapis.com/css?family=Lato:900' rel='stylesheet' type='text/css'>
    	
    	<!-- JS and jQuery stuff -->
    	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script> <!--jQuery Library-->
        <script type="text/javascript" src="js/jquery-ui.min.js"></script> <!--jQuery UI-->
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places"></script> <!--google maps places (for autocomplete)-->
        <script src="js/myscripts/mainEditProjectJS.js"></script> <!--*********************- include JS file for this page *********************** -->

        <!--tab icon-->
        <link rel="shortcut icon" href="images/tab_icon.ico">
    </head>
    <body>
        <div id="container"> <!--closed in footer-->
        	<?php include("templates/header.php"); 
                //$userId set in header, $projectUserId retrieved in query above
                if($projectUserId != $userId)  { //not the user that created the project, then cannot edit = redirect to home page
                    header("Location: localhost/Treeboks/index.php"); /*###########################this needs to be updated############*/
                }
            ?>
			<!--***************************-Beginning Page-******************************-->
			
            <div id="page_content">
                <!--***********************start content**********************-->
                <div id="edit_project">
                    <?php
                    if(isset($_GET['delete']) && $_GET['delete'] == 'true'){
                        $query = "DELETE FROM projects
                                WHERE project_id = {$projectId}";
                        executeQuery($query, "Project deleted, redirecting to My Projects page..."); //false means don't display error message
                        ?>
                        <script type="text/javascript">
                            setTimeout(
                                function(){ window.location.replace("my_projects.php"); }, 
                                2000 // 2 second delay
                            );
                        </script>
                        <?php
                    }

                    $projectSubmitted = false;
                    if (!empty($_POST)){ //Check if project_form.php is submitted
                        //debugging $projectLocLat = $_POST['hidden_loc_address']; print $projectLocLat;}
                        //Validate form
                        $problems = false;
                        $error_message = "";
                        $errorCounter = 0;

                        function validateForm($form_element, $errorMessage){
                            if (empty($form_element)) {
                                $problems = true;
                                $errorCounter++;
                                return "<li class='error'>{$errorMessage}</li>";
                            }
                            return "";
                        }
                        //Check if any required fields are empty
                        $error_message .= validateForm($_POST['project_name'], "Please give your project a name.");
                        $error_message .= validateForm($_POST['project_description'], "Please give your project a description.");
                        $error_message .= validateForm($_POST['project_loc'], "Please give your project a location.");
                        if ($_POST['hidden_loc_lat'] == 0) {
                            $problems = true;
                            $errorCounter++;
                            $error_message .= "<li class='error'>Please provide a valid location.</li>";
                        }

                        //Validate date
                        if(isset($_POST['project_date'])){
                            $dateRegExp = '/^((((0[13578])|([13578])|(1[02]))[\/](([1-9])|([0-2][0-9])|(3[01])))|(((0[469])|([469])|(11))[\/](([1-9])|([0-2][0-9])|(30)))|((2|02)[\/](([1-9])|([0-2][0-9]))))[\/]\d{4}$|^\d{4}$/';
                            if (empty($_POST['project_date'])) {
                                $problems = true;
                                $error_message .= '<li class="error">Please enter a date for your project.</li>';
                            } elseif (!preg_match($dateRegExp, $_POST['project_date'])) {
                                $problems = true;
                                $error_message .= '<li class="error">Please enter a valid date for your project (mm/dd/yyyy).</li>';
                            }
                        }

                        /*************************If no problems execute query*****************/
                        if (!$problems) {
                            //Set INSERT query variables
                            $projectName = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['project_name']))));
                            $projectDescription = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['project_description']))));
                            
                            //Get the location values
                            $projectLocLat = $_POST['hidden_loc_lat'];
                            $projectLocLng = $_POST['hidden_loc_lng'];
                            $projectLocAddress = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['hidden_loc_address']))));
                            $projectLocCity = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['hidden_loc_city']))));
                            $projectLocState = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['hidden_loc_state']))));
                            $projectLocZip = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['hidden_loc_zip']))));
                            $projectLocCountry = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['hidden_loc_country']))));
                            $projectLocFormattedAddress = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['hidden_loc_formatted_address']))));
                            
                            if(isset($_POST['select_hour'])){
                                //Format the Time
                                $projectTimeMin = "";
                                if($_POST['select_minute'] == 0){
                                    $projectTimeMin = "0" . $_POST['select_minute'];
                                } else
                                    $projectTimeMin = $_POST['select_minute'];
                                $projectTime = $_POST['select_hour'] . ':' . $projectTimeMin . ' ' . $_POST['select_period'];
                            } else{
                                $projectTime = date_format($dt, "g:i A");
                            }

                            if(isset($_POST['project_date'])){
                                //Make a DateTime object (NOTE that the time is also stored separately)
                                $projectDate = mysql_real_escape_string(htmlentities(trim(strip_tags($_POST['project_date']))));
                                $projectDate = new DateTime($projectDate . ' ' . $projectTime); //convert to date time formatting
                                $projectDate = $projectDate->format('Y-m-d H:i'); //convert back to string (trust me this is necessary, mysql will not do it for you)
                            } else{
                                $projectDate = date_format($dt, 'Y-m-d H:i');
                            }

                            //Define query (note that $userId is retrieved by header.php)
                            $query = "UPDATE projects
                                SET project_name = '$projectName', 
                                    project_description = '$projectDescription', 
                                    project_datetime = '$projectDate', 
                                    project_time = '$projectTime', 
                                    location_lat = '$projectLocLat', 
                                    location_lng = '$projectLocLng', 
                                    location_address = '$projectLocAddress', 
                                    location_city = '$projectLocCity', 
                                    location_state = '$projectLocState', 
                                    location_zipcode = '$projectLocZip', 
                                    location_country = '$projectLocCountry', 
                                    loc_formatted_address = '$projectLocFormattedAddress'
                                WHERE project_id = {$projectId}";
                            executeQuery($query, "Project update successful, redirecting to project page...", false); //false means don't display error message

                            //for sticky form to clear form
                            $projectSubmitted = true;
                            ?>
                            <script type="text/javascript">
                                setTimeout(
                                    function(){ window.location.replace("view_project.php?proj_id=" + <?php echo $projectId; ?>); }, 
                                    2000 // 2 second delay
                                );
                            </script>
                            <?php
                        } elseif ($problems) { //Fields are empty or incorrect
                            $pluralErrors = "";
                            if($errorCounter == 1){
                                $pluralErrors = " is 1 error";
                            } else
                                $pluralErrors = " are {$errorCounter} errors";
                            print "<p class='error'>Please make sure you filled out the entire form correctly. There {$pluralErrors}.</p>
                                <ul class='ul_error'>{$error_message}</ul><br/>";
                        } //end of query if
                    }//End of form submit if*/

                    $project_form_action = "edit_project.php?proj_id=" . $projectId;
                    $project_form_id = "edit_project_form";
                    $project_form_legend = "EDIT YOUR PROJECT";
                    $project_form_element_class = "edit_project";
                    $project_form_submit_button_value = "Done (back to My Projects)";

                    if(!isset($_GET['delete'])){
                        //fill in values in form with project values (a sort of "sticky"), see project_form.php for where these values are used
                        $edit_project_name = $projectName;
                        $edit_project_descript = $projectDescrip;
                        $edit_project_loc = $projectAddr;
                        $edit_project_date = $date;
                        $edit_project_hasExpired = $projectHasExpired;
                    }
                    include("templates/project_form.php"); 
                    ?>
                </div><!--*********End add_project div***********-->
            </div><!--end page_content div-->
        <script>
            /*Sticky the time-select menus*/
            $("select#select_hour option").each(function(){
                if($(this).val() == <?php echo $projectStickyHour ?>){
                    $(this).attr('selected', 'selected');
                }
            });
            $("select#select_minute option").each(function(){
                if($(this).val() == <?php echo $projectStickyMin ?>){
                    $(this).attr('selected', 'selected');
                }
            });


            $( "#delete_dialog" ).dialog({
                resizable: false,
                autoOpen: false,
                width: 300,
                height: 140,
                modal: true,
                buttons: {
                    "Yes": function() {
                        window.location.replace("edit_project.php?proj_id=" + <?php echo $projectId; ?> + "&delete=true"); /*####################this needs to URL needs to be updated########################*/
                        $(this).dialog("close");
                    },
                    "Cancel": function() {
                        $(this).dialog("close");
                    }
                }
            });
            /*---------------Delete button listener------------------*/
            $("button#delete_project").click(function(){
                $("#delete_dialog").dialog('open');
            });
        </script>
        
<?php include("templates/footer.php"); ?>