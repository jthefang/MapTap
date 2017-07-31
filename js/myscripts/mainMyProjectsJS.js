function adjustContentHeight(){
    if($("div#page_content").height() < .89 * $("div#container").height()){ //less than a full page
    	$("div#page_content").height(.89 * $("div#container").height()); //make a full page
	} else{ //has overflow
		$("div#page_content").height($("div#page_content").height()); //make it equal back to itself
	}
}
$(document).ready(function(){
	adjustContentHeight();
}); //end ready() doc
$(window).load(function(){ //HAS TO BE IN $(window).load to make sure all page elements are fully loaded
	
});