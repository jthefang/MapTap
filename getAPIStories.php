<?php 
include("phpfunctions/mainfunctions.php"); 
session_start(); //for facebook login (set up in "header.php")
?>

<!DOCTYPE html>
<html>
    <head>
        <title>MapTap</title>
    
        <!-- style stuff -->
        <link type="text/css" rel='stylesheet' href='css/mystyles/mainIndexStyle.css' /> <!--this page's style stuff-->
        <link href="css/jquery-ui.min.css" type="text/css" rel="stylesheet" /><!--jQuery UI style-->
        <link href="js/raty-2.7.0/lib/jquery.raty.css" type="text/css" rel="stylesheet" /><!--Raty (star ratings) style-->
        <!-- fonts -->
        <link href='http://fonts.googleapis.com/css?family=Lato:900' rel='stylesheet' type='text/css'> <!--Lato Font-->
        
        <!-- JS and jQuery stuff -->
        <script src="http://code.jquery.com/jquery-1.8.3.min.js"></script> <!--jQuery Library-->
        <script type="text/javascript" src="js/jquery-ui.min.js"></script> <!--jQuery UI-->
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places"></script> <!--google maps places (for autocomplete)-->
        <script src="js/oms.min.js"></script> <!--OverlappingMarkerSpiderfier library-->
        <script type="text/javascript" src="js/raty-2.7.0/lib/jquery.raty.js"></script> <!--Raty (star ratings) library-->
        <!--*********************- include JS file for index page *********************** -->

        <!--tab icon-->
        <link rel="shortcut icon" href="images/tab_icon.ico">
    </head>
    <body>
        <div id="container">
            <?php include("templates/header.php"); ?>
            <!--***************************-Beginning Page-******************************-->
            <!--<div id="left_pane">
                <?php //include("templates/add_project_form.php"); ?> <!-******div id="add_project"-->
                <!--<div id="ads"></div>->
            </div>-->

            <div id="page_content">
                hi
            </div>
            
<?php include("templates/footer.php"); ?>

<script>
    $(document).ready(function() {
        var apikey = "9b5092187b299bcb882b9a596a51abf5:18:74631831";
        var geocoder = new google.maps.Geocoder(); //auto complete in location input prompt
        var url = "http://api.nytimes.com/svc/topstories/v2/world.json?api-key=" + apikey;
        $.getJSON(url, function(data) {
            // Loop through each feed entry
            $.each(data.results, function(i,item) { //results is a key to a nested array within the returned JSON data
                setTimeout(function(){
                    var url, title, datepublished, author, abstract, geoloc, imgUrl = "null";
                    // Get the URL for the article
                    url = item.url;
                    // Get the title of the article
                    title = item.title;
                    // Date in the format: YYYY-MM-DDTHH:MM:SS.000z (can use var d = new Date(); var n = d.toDateString();)
                    datepublished = item.updated_date;
                    // Get the author name
                    author = item.byline;
                    // Get the abstract for the article
                    abstract = item.abstract;
                    //Get the img
                    $.each(item.multimedia, function(i,media) { //loop through multimedia nested array within the item
                        if(media.format == "superJumbo"){
                            imgUrl = media.url;
                        }
                    });
                    
                    var loc = "" + item.geo_facet;
                    geoloc = loc.replace(/ *\([^)]*\) */g, "");

                    //geoloc = loc.substr(0, loc.indexOf(","));
                    console.log("title=" + title + 
                        "\ndate_published=" + datepublished + 
                        "\nauthor=" + author + 
                        "\nabstract=" + abstract + 
                        "\nurl=" + url +   
                        "\nloc=" + loc +
                        "\nimg_url= " + imgUrl + 
                        "\ngeoloc=" + geoloc + 
                        "\ntype=" + typeof geoloc);
                    $.ajax({
                        url: "phpfunctions/addStoryByAPI.php",
                        type: "POST",
                        data: {
                            "title"             : title,
                            "date_published"    : datepublished,
                            "author"            : author,
                            "abstract"          : abstract,
                            "geoloc"            : geoloc,
                            "img_url"           : imgUrl,
                            "url"               : url
                        },
                        /*type: "GET",
                        data: "title=" + title + 
                            "&date_published=" + datepublished + 
                            "&author=" + author + 
                            "&abstract=" + abstract + 
                            "&url=" + url +
                            "&lat=" + lat + 
                            "&lng=" + lng + 
                            "&formatted_address=" + formattedAddress,*/
                        dataType: "json",
                        error: function(xhr, status, error) {
                            console.log("Error: getNYTStories()" + xhr.status + " - " + error);
                            //change to console.log();
                        },
                        success: function(data) {
                            console.log("success: " + data.success);
                            if(typeof data.error != "undefined" || data.error != null){
                                console.log("error: " + data.error);
                            }
                        } //end success for addStoryByAPI
                    }); //end ajax to input NYT stories into server*/

                    // Construct the display for the video
                    var text =                  
                        "<a href='" + url + "'>" + title + "</a><br>" +
                        "Published: " + datepublished + " by " + author + "<br><br>";
                    // Append text string to the div for display
                    //$("div#nytstory").append(text);
                }, 2000);//end setTimeout()  
            }); //end loop through each JSON result
        }); //end getJSON
    });//end document.ready()      
</script> <!--main JS for this page-->

<?php
// remove stories that are over a month old (don't clutter my DB!)
$query = "DELETE FROM stories WHERE story_datetime < DATE_SUB(NOW(), INTERVAL 1 MONTH)";
executeQuery($query, "error deleting old stories", false);

// also remove stories with no location
$query = "DELETE FROM stories WHERE location_lat = 0.0 AND location_lng = 0.0)";
executeQuery($query, "error deleting stories w/ no loc", false);
?>