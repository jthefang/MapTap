/**
	TODO: toggle locations of news stories: (clicking on US should hide world news, but you should still be able to see world news again somehow)
*/

var markers = []; //array of markers (will be filled by server ajax request)
var map;
var oms; //spidifier

var allowedLocations = { //array of acceptable map locations used in setBoundsOnMap() below
	theWorld:new google.maps.LatLngBounds(
		new google.maps.LatLng(-89, -180), //sw corner (has to be 89 or you can scroll to deep on top/bot)
		new google.maps.LatLng(89, 180) //ne corner
	)
	/*unitedStates:new google.maps.LatLngBounds(
	    new google.maps.LatLng(15, -179), //sw corner
     	new google.maps.LatLng(75, -50) //ne corner
	),
	singaporeMalaysia:new google.maps.LatLngBounds(
	    new google.maps.LatLng(-2, 95), //sw corner
     	new google.maps.LatLng(5, 110) //ne corner
	)*/
}; 
var usa = {
	nwlat: 49.5,
	nwlng: -124.733,
	selat: 25.2,
	selng: -66.95,
	centerLat: 39.8333333,
	centerLng: -98.585522
}

function getMarkersForMap(postData={}){ //oms being the OverlappingMarkerSpiderfier
	var defaultMarkerIcon = "images/marker_news_alert.png";
	var activeMarkerIcon = "images/marker_news_alert_active.png";

	postData.getmarkers = "yes"; //Array

	/***-------------------------------populating the map with markers---------------------------***/
    //send ajax requesting markers
    $.ajax({
        url: "phpfunctions/getmarkers.php",
        type: "POST",
        data: postData,
        dataType: "json",
		error: function(xhr, status, error) {
			console.log("Error: getMarkersForMap() " + xhr.status + " - " + error);
		},
		success: function(data) {
			$.each(data, function(index, value) {
				var markerLat = value.lat;
				var markerLng = value.lng;
				var storyName = "" + value.story_headline;
				var storyId = value.story_id;

				var marker = new google.maps.Marker({
					position: new google.maps.LatLng(markerLat, markerLng),
					title: storyName, //will show up on user hover over marker
					icon: defaultMarkerIcon,
					map: map		
				});
				marker.id = storyId; //set the marker id
				markers.push(marker); //add the marker to the array of markers
				oms.addMarker(marker);

				var markerListener = google.maps.event.addListener(marker, "click", function(event){
				//oms.addListener('click', function(marker, event) {
					/*reset all markers back to default icon*/
					var i;
					for(i = 0; i < markers.length; ++i){
						markers[i].setIcon(defaultMarkerIcon); //default icon
						markers[i].setZIndex(1); //appear on bottom of stack
					}
					//change the icon of the selected marker so that user knows which story was selected
					marker.setIcon(activeMarkerIcon);
					marker.setZIndex(10); //appear on top of stack

					/*****move map position****/
					map.setCenter(marker.getPosition());
					/*if(map.getZoom() < 8){
						map.setZoom(8);
					}*/
					
					//send ajax requesting data based on storyId (of marker)
					$.ajax({
				        url: "phpfunctions/getStoryById.php",
				        type: "GET",
				        data: "getstoryId=" + marker.id,
				        dataType: "json",
						error: function(xhr, status, error) {
							console.log("Error: getStoryById in getMarkersForMap()" + xhr.status + " - " + error);
							//change to console.log();
						},
						success: function(data) {
							/* data retrieved:
							"story_id" : story_id,
							"user_id" : user_id,
				    		"story_headline" : story_headline,
				    		"story_text" : story_text,
				    		"story_date" : story_date (formatted),
				    		"story_address" : story_address (formatted), 
							"lat" : location_lat,
							"lng" : location_lng,
							"url" : url_api,
							"img_url" : img_url
							*/
							var storyName = data.story_headline;
							var storyDescrip = data.story_text;
							var storyDate = data.story_date;
							var storyAddr = data.story_address;
							var imgUrl = data.img_url;
							var imgNull = "block";
							if(imgUrl == "null"){
								imgNull = "none";
								imgUrl = "#";
							}

							//actual message
							var html = "<a id='close' href='#'></a>" //close symbol onclick closes the map message (see code below and mainIndex css for more)
								+ "<h1 id='message_storyName'><a target='_blank' href='" + data.url + "'>" + storyName + "</a></h1>";
							/*if(avgRating > 0){
								html += "<div id='message_storyRating'></div><span id='storyRating'>" + avgRating + " out of 5 (<a href='#'>" + totalRatings + "</a>)</span>";
							}*/
							html += "<p id='message_storyDate'><i>" + storyDate + "</i></p>"
								+ "<p id='message_storyAddress'><b>" + storyAddr + "</b></p>"
								+ "<img src='" + imgUrl + "' id='message_storyImg' style='display:" + imgNull + ";'/>"
								+ "<p id='message_storyDescription'>" + storyDescrip + "</p>"
								+ "<p id='message_viewstoryPageLink'><a target='_blank' href='" + data.url + "'>View article</a><br/></p>";
								//+ "<p id='message_viewstoryPageLink'><a target='_blank' href='view_story.php?proj_id=" + marker.id + "'>Get directions/Go to story page</a><br/></p>";
							setMapMessage(html, defaultMarkerIcon);
							$("#map_message").css("z-index", 5);
						} //end success for markerListener
				    }); //end ajax for marker listener
				});  // end markerListener
			}); //end $.each()
		} //end success for getting all the markers
    }); //end ajax for getting all the markers
}

function setMapMessage(message, defaultMarkerIcon){
	//Create custom message
	var overlay = new google.maps.OverlayView();
	overlay.draw = function() {
		$("#map_message").empty().html(message).show();
		setMessageCloseable(defaultMarkerIcon);

		//make sure the image is not too big as to not fit
		$('img#message_storyImg').on('load', function () {
		 	var maxImageHeight = $(window).height() * 3/8;
			if($("img#message_storyImg").height() > maxImageHeight){
				$("img#message_storyImg").height(maxImageHeight);
				$("img#message_storyImg").width("auto");
			}
		});
		
		/*get the position for the map_message
			(needs to be after there is something to show*/
		//get the coordinates of the map (used to set X and Y of the map_message)
		var mapPosition = $("#map_canvas").position(); 
		//to calculate the X coordinate
		var mapWidth = $("#map_canvas").width();
		var messageWidth = $("#map_message").outerWidth(true); //full width
		
		var mapContainerX = mapPosition.left + ((.999 * mapWidth) - messageWidth);
		var mapContainerY = 1.7 * mapPosition.top;

		$("#map_message").css({
			top: mapContainerY,
			left: mapContainerX 
		});
	};
	overlay.setMap(map);

	map.addListener('drag', function() { //prevent the flitting of the html after the map_message changes and user drags map
	    if($("#map_message").is(":visible")){
	    	$("#map_message").html(message);

	    	setMessageCloseable(defaultMarkerIcon);
	    }
	});
	map.addListener('zoom_changed', function() { //prevent the flitting of the html after the map_message changes and user drags map
	    if($("#map_message").is(":visible")){
	    	$("#map_message").html(message);

	    	setMessageCloseable(defaultMarkerIcon);
	    }
	});
}

function setMessageCloseable(defaultMarkerIcon){
	$("a#close").click(function(){
		$("#map_message").empty().hide().css("z-index", -1);
		/*reset all markers back to default icon*/
		var i;
		for(i = 0; i < markers.length; ++i){
			markers[i].setIcon(defaultMarkerIcon); //default icon
			markers[i].setZIndex(1); //appear on bottom of stack
		}
	});

	//let esc character also exit (set the listener for a one time fling)
	google.maps.event.addDomListenerOnce(document, 'keydown', function (e) { 
	    var code = (e.keyCode ? e.keyCode : e.which);
	    if (code === 27) {
	        $("a#close").trigger("click");
	    }
	});
}

function findCurrentAllowedLoc(){
	var allowedLoc = allowedLocations.theWorld; //default to unitedStates
	$.each(allowedLocations, function(index, value){ //only allow the map to pan to allowedLocations (array created at the top: for now USA and Singapore/Malaysia only)
		if(value.contains(map.getCenter())){
			allowedLoc = value;
			return value;
		}
	});
	return allowedLoc;
}

function setBoundsOnMap(){ /*-----------restrict user from scrolling outside allowedLocations bounds------------*/
	var lastValidCenter = map.getCenter();

	google.maps.event.addListener(map, 'center_changed', function() {
		var bounds = findCurrentAllowedLoc();

	    if (bounds != null && bounds.contains(map.getCenter())) {
	        // still within valid bounds, so save the last valid position
	        lastValidCenter = map.getCenter();
	        return;
	    }

	    // not valid anymore => return to last valid position
	    map.panTo(lastValidCenter);
	});
}

function toggleLocSelector(){ //changes the map according to the location selector list on top right corner of map
	var currentAllowedLoc = findCurrentAllowedLoc();
    if(currentAllowedLoc == allowedLocations.unitedStates){
    	$("li#united_states").css("font-weight", "bold");
    	$("li#singapore_malaysia").css("font-weight", "normal");
    } else if(currentAllowedLoc == allowedLocations.singaporeMalaysia){
    	$("li#united_states").css("font-weight", "normal");
    	$("li#singapore_malaysia").css("font-weight", "bold");
    }
}

/*function getNearbyProjs(latitude, longitude, markers){
	$.ajax({
        url: "phpfunctions/getListOfNearbystorys.php",
        type: "GET",
        data: "userLat=" + latitude + "&userLng=" + longitude,
        dataType: "json",
		error: function(xhr, status, error) {
			alert("Error: getNearbyProjs() " + xhr.status + " - " + error);
		},
		success: function(data) {
			if (data == null) {
                $("#display_nearby_projs").html("<h3>There are no storys in this area. <a href='add_story.php'>Add one now!</a></h3>");
            } else {
				var html = "<ul id='nearby_projs_list'>";
				$.each(data, function(index, value) {
					/* data retrieved:
						"story_id" : story_id,
						"user_id" : user_id,
			    		"story_name" : story_name,
			    		"story_description" : story_description
			    		"story_date" => date,
			    		"story_time" =>  story_time,
			    		"proximity_to_user" : proximity of story (calculated by php script)
					* /
					var storyName = value.story_name;
					var storyDescrip = value.story_description;
					var storyId = value.story_id;
					var storyDate = value.story_date;
					var storyTime = value.story_time;
					var storyProx = value.proximity_to_user;
					html +=	"<li><h2 class='nearby_projs_storyName'><a href='" + storyId + "'></a>" 
								//link click event set below
								+ storyName + "</h2>" 
						+ "<p class='nearby_projs_storyDescrip'>" + storyDescrip + "</p>"
						+ "<p class='nearby_projs_datetime'>" + storyTime + " on <i>" + storyDate + "</i></p>"
						+ "<p class='nearby_projs_prox'><b>" + storyProx + " miles away</b></p></li>"
				}); //end $.each
				html += "</ul>";
				$("#display_nearby_projs").html(html); 

				//set the links to trigger the click event of the marker with the id = the link's href value	
			    $("#storys_near_you a").click(function(evt){
			    	evt.preventDefault(); //cancel default of link taking you to a new page
			    });
			    $("#storys_near_you li").click(function() {
					var selectedMarkerId = $(this).find("a:first-child").attr("href");
					
					//loop through array of markers until the one with the id
					var i;
					for(i = 0; i < markers.length; ++i){
						if(markers[i].id == selectedMarkerId){
							//NOTE: triggered twice, just in case the marker is spiderfied
							//in which case the first click would unspiderfy, and the second click would select that marker
							google.maps.event.trigger(markers[i], 'click');
							google.maps.event.trigger(markers[i], 'click');
						}
					}
				});
			} //end if data null
		} //end success
    }); //end ajax* /
}*/

var usaOverlay;
function initMapOverlay() {
	var imageBounds = {
	    north: 49.5,
	    south: 25.2,
	    east: -66.95,
	    west: -124.73333
	};

	usaOverlay = new google.maps.GroundOverlay(
		'images/us_cutout.png',
	    imageBounds);
	usaOverlay.setMap(map);
	with ({map: map}) {
		google.maps.event.addListener(usaOverlay, 'click', usaClick);
	}
}

function usaClick() {
	var data = {
		nwlat: usa.nwlat,
		nwlng: usa.nwlng,
		selat: usa.selat,
		selng: usa.selng
	};
	clearMakers();
	console.log(markers);
	getMarkersForMap(data);
	map.setCenter(new google.maps.LatLng({lat: usa.centerLat, lng: usa.centerLng}));
	map.setZoom(4);
}

function clearMakers() {
	for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(null);
    }
    markers = [];
}

function initMap() {
	/******************** MAP stuff********************/
    var defaultLocation = "USA"; //initial location

	//create map
	var mapOptions = {
		zoom: 3,
        minZoom: 3,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map($("#map_canvas").get(0), mapOptions);

   	setBoundsOnMap();//vertically bound the map, restrict user from scrolling outside

   	//Initialize spiderfier (for the case where there are multiple markers in one loc)
   	oms = new OverlappingMarkerSpiderfier(map, {
		markersWontMove: true, 
		markersWontHide: true,
		keepSpiderfied: true
	});

	var geocoder = new google.maps.Geocoder();
	geocoder.geocode({address : defaultLocation}, function(results) {
		var defaultLatLng = results[0].geometry.location;
		map.setCenter(defaultLatLng);

		/*********************--------location search box (see bottom of code also)---------********************/
		var input = document.getElementById('location_search');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        input.style.display = "block"; //display only after it is in position

        /***-------------------------------populating the map with markers---------------------------***/
        getMarkersForMap();

        /*---------------------------location search box----------------------*/
        var searchBox = new google.maps.places.SearchBox(input);
        google.maps.event.addListener(searchBox, 'places_changed', function() {
		    var places = searchBox.getPlaces();
		    var loc = places[0].geometry.location;
		    map.setCenter(loc);
		    map.setZoom(10)
		    /*getNearbyProjs(loc.lat(), loc.lng(), markers);*/
		});

		initMapOverlay();
	}); //end geocoder.geocode();
}

$(document).ready(function() {
	google.maps.event.addDomListener(window, 'load', initMap);

	$("div#footer").hide();
	//you can't just hide because any height of the footer div causes the setFooterHeight in mainJS.js to make the page content height "auto"
	$("div#footer").css("height", 0).css("line-height", 0).css("overflow", "hidden").css("padding", 0).css("margin", 0).css("border", "0px solid white"); 
});//end document.ready()
function setMapHeight(){
    var pageMarginalHeight = window.innerHeight - $("div#header_bar").outerHeight(true); //the height left in the window (after header bar is rendered)
   	$('div#page_content').css("height", pageMarginalHeight);    
    //alert("windowHeight: " + window.innerHeight + " | headerHeight: " + $("div#header_bar").outerHeight(true) + " | pageMarginalHeight: " + pageMarginalHeight);
}
$(window).load(function(){ //make sure that ALL items on page TRULY load 
	//must wait for the header bar to load before setting the footer (account for header's height)
    /**********************adjusting the footer**********************/
    setMapHeight(); //take up the rest of the window height
});