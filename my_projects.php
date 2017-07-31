<?php 
include("phpfunctions/mainfunctions.php"); //connects to database
session_start(); //for facebook login (set up in "header.php")

function populateArray($query, $expiredArray, $activeArray){
    
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>My Projects</title>
    
    	<!-- style stuff -->
        <link type="text/css" rel='stylesheet' href='css/mystyles/mainMyProjectsStyle.css'> <!--this page's style stuff-->
        <link href="css/jquery-ui.min.css" type="text/css" rel="stylesheet"><!--jQuery UI style-->
        <!-- fonts -->
        <link href='http://fonts.googleapis.com/css?family=Lato:900' rel='stylesheet' type='text/css'>
    	
    	<!-- JS and jQuery stuff -->
    	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script> <!--jQuery Library-->
        <script type="text/javascript" src="js/jquery-ui.min.js"></script> <!--jQuery UI-->
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places"></script> <!--google maps places (for autocomplete)-->
        <!--*********************- include JS file for index page *********************** -->
		<script src="js/myscripts/mainMyProjectsJS.js"></script>

        <!--tab icon-->
        <link rel="shortcut icon" href="images/tab_icon.ico">
    </head>
    <body>
        <div id="container"> <!--closed in footer-->
        	<?php include("templates/header.php"); ?>
			<!--***************************-Beginning Page-******************************-->
			
            <div id="page_content">
                <?php //get user's active projects
                //$userId variable used below is set in "header.php"

                /*------------------------------------retrieve all member projects-------------------------*/
                $expiredMemberProjects = array();
                $activeMemberProjects = array();
                $projectMemberQuery = "SELECT project_id FROM project_members
                        WHERE participant_id = {$userId}"; 
                if ($projectMemberResult = @mysql_query($projectMemberQuery, $dbc)) { //successful query
                    if (mysql_num_rows($projectMemberResult) > 0) {
                        while ($projectMemberRow = mysql_fetch_array($projectMemberResult)) {
                            $projectId = $projectMemberRow['project_id'];
                            $projectInfoQuery = "SELECT * FROM projects
                                WHERE project_id = {$projectId}
                                ORDER BY project_datetime ASC"; 
                            if ($result = @mysql_query($projectInfoQuery, $dbc)) { //successful query
                                while ($row = mysql_fetch_array($result)) {
                                    $projectId = $row['project_id'];
                                    $projectName = $row['project_name'];
                                    $projectTime = $row['project_time'];
                                    $projectAddr = $row['loc_formatted_address'];
                                    
                                    //format the description
                                    $projectDescript = $row['project_description'];
                                    if(strlen($projectDescript) > 170){
                                        $abridged = substr($row['project_description'], 0, 170);
                                        $cutoff = strrpos($abridged, ' '); //find the end of the last word
                                        $projectDescript = substr($row['project_description'], 0, $cutoff) . "...";
                                    }

                                    //format datetime
                                    $dt = date_create($row['project_datetime']);
                                    $date = date_format($dt, 'l F jS, Y');

                                    if($row['hasExpired'] == 1){
                                        $expiredMemberProjects[] = array(
                                            "project_id" => $projectId,
                                            "project_name" => $projectName,
                                            "project_time" => $projectTime,
                                            "project_date" => $date,
                                            "project_addr" => $projectAddr,
                                            "project_descript" => $projectDescript
                                        );
                                    } else{
                                        $activeMemberProjects[] = array(
                                            "project_id" => $projectId,
                                            "project_name" => $projectName,
                                            "project_time" => $projectTime,
                                            "project_date" => $date,
                                            "project_addr" => $projectAddr,
                                            "project_descript" => $projectDescript
                                        );
                                    }
                                } //End of while $projectInfoQueryRow loop* /
                            } //end of if $projectInfoResult
                        } //end of while $projectMemberQueryRow

                        $no_member_projects = false;
                    } else{
                        $no_member_projects = true;
                    }//end if result > 0
                } else { //Query didn't run (later redirect to an error page) /*#######################change this to error page##########################*/
                    print '<p style="border: red; color: red;">Error, something occurred which prevented the query from executing. ' 
                        . mysql_error($dbc) . '</p>';
                }

                /*----------------------------------retrieve all hosted projects----------------------------------*/
                $expiredHostedProjects = array();
                $activeHostedProjects = array();
                $query = "SELECT * FROM projects
                        WHERE user_id = {$userId}
                        ORDER BY project_datetime ASC"; 
                if ($result = @mysql_query($query, $dbc)) { //successful query
                    if (mysql_num_rows($result) > 0) {
                        while ($row = mysql_fetch_array($result)) {
                            $projectId = $row['project_id'];
                            $projectName = $row['project_name'];
                            $projectTime = $row['project_time'];
                            $projectAddr = $row['loc_formatted_address'];
                            
                            //format the description
                            $projectDescript = $row['project_description'];
                            if(strlen($projectDescript) > 170){
                                $abridged = substr($row['project_description'], 0, 170);
                                $cutoff = strrpos($abridged, ' '); //find the end of the last word
                                $projectDescript = substr($row['project_description'], 0, $cutoff) . "...";
                            }

                            //format datetime
                            $dt = date_create($row['project_datetime']);
                            $date = date_format($dt, 'l F jS, Y');

                            if($row['hasExpired'] == 1){
                                $expiredHostedProjects[] = array(
                                    "project_id" => $projectId,
                                    "project_name" => $projectName,
                                    "project_time" => $projectTime,
                                    "project_date" => $date,
                                    "project_addr" => $projectAddr,
                                    "project_descript" => $projectDescript,
                                    "is_host" => true
                                );
                            } else{
                                $activeHostedProjects[] = array(
                                    "project_id" => $projectId,
                                    "project_name" => $projectName,
                                    "project_time" => $projectTime,
                                    "project_date" => $date,
                                    "project_addr" => $projectAddr,
                                    "project_descript" => $projectDescript
                                );
                            }
                        } //End of while $projectInfoQueryRow loop* /

                        $no_hosted_projects = false;
                    } else{ //no hosted projects
                        $no_hosted_projects = true;
                        //echo "<p class='no_results'>You have no hosted or active projects currently. You can <a href='add_project.php'>start one</a> or <a href='index.php'>browse the homepage for one to join</a></p>";
                    } //end if result > 0
                } else { //Query didn't run (later redirect to an error page) /*#######################change this to error page##########################*/
                    print '<p style="border: red; color: red;">Error, something occurred which prevented the query from executing. ' 
                        . mysql_error($dbc) . '</p>';
                }

                /*----------------------------------retireve user active member projects-------------------------------*/
                
                if(count($activeMemberProjects) > 0){
                    echo '<div id="my_active_projs">
                        <span class="projs_label">MY ACTIVE PROJECTS</span>
                        
                        <!--Table of projects-->
                        <table class="projs_table">
                            <tbody class="projs_tbody">';

                    $colCount = 0; // limit the number of columns in a table row, heres the iteration counter
                    foreach($activeMemberProjects as $value){ //NOTE that this is a multidimnesional array
                        if($colCount == 0){
                            echo '<tr class="projs_tr">';
                        }

                        echo "<td class='projs_td activeMember_proj'>";

                        echo "<h1 class='projs_projectName'><a href='view_project.php?proj_id=" . $value['project_id'] . "'>" 
                            . $value['project_name'] . "</a></h1>"
                            . "<p class='projs_projectDateTime'>" . $value['project_time'] . " on <i>" . $value['project_date'] . "</i></p>"
                            . "<p class='projs_projectAddr'>@ " . $value['project_addr'] . "</p>"
                            . "<p class='projs_projectDescript'>" . $value['project_descript'] . "</p>";

                        echo "</td>";

                        $colCount++;
                        if($colCount > 2){ //limit of 3 projects listed in 1 table row
                            echo "</tr>";
                            $colCount = 0; //reset counter
                        } //end if colCount > 2
                    } //end foreach activeMemberProject
                    echo '</tbody></table>
                        </div>'; //end div and table

                    $no_activeMember_projects = false;
                } else{
                    $no_activeMember_projects = true;
                }
                

                /*------------------------------Get users active hosted projects----------------------------*/
                //retireve user active projects
                if(count($activeHostedProjects) > 0){
                    echo '<div id="my_hosted_projs">
                                <span class="projs_label">MY HOSTED PROJECTS</span>

                                <!--Table of projects-->
                                <table class="projs_table">
                                    <tbody class="projs_tbody">';
                    $colCount = 0; // limit the number of columns in a table row, heres the iteration counter
                    foreach($activeHostedProjects as $value){ //NOTE that this is a multidimnesional array
                        if($colCount == 0){
                            echo '<tr class="projs_tr">';
                        }

                        echo "<td class='projs_td'>";

                        echo "<h1 class='projs_projectName'><a href='view_project.php?proj_id=" . $value['project_id'] . "'>" 
                            . $value['project_name'] . "</a></h1>"
                            . "<p class='projs_projectDateTime'>" . $value['project_time'] . " on <i>" . $value['project_date'] . "</i></p>"
                            . "<p class='projs_projectAddr'>@ " . $value['project_addr'] . "</p>"
                            . "<p class='projs_projectDescript'>" . $value['project_descript'] . "</p>"
                            . "<p class='edit_project_p'><a href='edit_project.php?proj_id=" . $value['project_id'] . "'><span class='edit_link'>Edit</span><img src='images/edit_project_icon.png' class='edit_icon'/><a/></p>";

                        echo "</td>";

                        $colCount++;
                        if($colCount > 2){ //limit of 3 projects listed in 1 table row
                            echo "</tr>";
                            $colCount = 0; //reset counter
                        } //end if colCount > 2
                    } //end foreach activeMemberProject
                    echo '</tbody></table>
                        </div>'; //end my active projs div

                    $no_activeHosted_projects = false;
                } else{
                    $no_activeHosted_projects = true;
                }
 
                //prompt user to some action
                if($no_activeHosted_projects && $no_activeMember_projects){
                    echo '<div id="my_active_projs">
                        <span class="projs_label">MY ACTIVE PROJECTS</span>

                        You have no active projects currently. You can <a href="add_project.php">start one</a> or <a href="index.php">browse the homepage</a> for one to join.
                        </div>';
                }

                /*-----------------------------Get users past projects (expired)-------------------------------*/
                //retireve user project memorial
                $expiredArrays = array_merge($expiredHostedProjects, $expiredMemberProjects);
                function dateCompare($a, $b){ //sort by date
                    $t1 = strtotime($a['project_date']);
                    $t2 = strtotime($b['project_date']);
                    return $t1 - $t2;
                }    
                usort($expiredArrays, 'dateCompare');
                $expiredArrays = array_reverse($expiredArrays);

                if(count($expiredArrays) > 0){
                    echo '<div id="proj_memorial">
                            <span class="projs_label">PROJECT MEMORIAL<br/>
                                <span class="projs_label_description">(Your past projects)</span>
                            </span>

                            <!--Table of projects-->
                            <table class="projs_table">
                                <tbody class="projs_tbody">'; 

                    $colCount = 0; // limit the number of columns in a table row
                    foreach($expiredArrays as $value){
                        if($colCount == 0){
                            echo '<tr class="projs_tr">';
                        }

                        echo "<td class='projs_td proj_memorial'>"; //note that the table data here has TWO classes

                        echo "<h1 class='projs_projectName'><a href='view_project.php?proj_id=" . $value['project_id'] . "'>" 
                            . $value['project_name'] . "</a></h1>"
                            . "<p class='projs_projectDateTime'>" . $value['project_time'] . " on <i>" . $value['project_date'] . "</i></p>"
                            . "<p class='projs_projectAddr'>@ " . $value['project_addr'] . "</p>"
                            . "<p class='projs_projectDescript'>" . $value['project_descript'] . "</p>";
                            
                        if(isset($value['is_host'])){
                            echo "<p class='edit_project_p'><a href='edit_project.php?proj_id=" . $value['project_id'] . "'><span class='edit_link'>Edit</span><img src='images/edit_project_icon.png' class='edit_icon'/><a/></p>";
                        }

                        echo "</td>";

                        $colCount++;
                        if($colCount > 2){ //limit of 3 projects listed in 1 table row
                            echo "</tr>";
                            $colCount = 0; //reset counter
                        }
                    } //End of foreach loop* /

                    echo '</tbody></table></div>'; //end table and div
                } //End if(count($expiredArrays) > 0)
                ?>
            </div><!--end page_content div-->

<?php include("templates/footer.php"); ?>