function toggleUserProfileOptions(){
	$("div#user_options_menu_div").toggle();
	$("div#user_profile").toggleClass("user_profile_selected");

	//var url = $('img#show_more_icon')[0].src;
	$("div#user_profile").toggleClass("fb_user_button_selected");

	$("img#show_more_icon").toggle();
	$("img#show_more_icon_selected").toggle();
}
function stickIt() { //sticky header bar
  	var orgElementPos = $('.original').offset();
  	orgElementTop = orgElementPos.top;               

  	if($(window).scrollTop() >= (orgElementTop)) {
	    // scrolled past the original position; now only show the cloned, sticky element.

	    // Cloned element should always have same left position and width as original element.     
	    orgElement = $('.original');
	    coordsOrgElement = orgElement.offset();
	    leftOrgElement = coordsOrgElement.left;  
	    widthOrgElement = orgElement.css('width');

	    $('.cloned').css('left', leftOrgElement + 'px').css('top', 0).css('width', widthOrgElement + 'px').show();
	    $('.original').css('visibility', 'hidden');
  	} else{
	    // not scrolled past the menu; only show the original menu.
	    $('.cloned').hide();
	    $('.original').css('visibility', 'visible');
  	}
}
function checkNotificationsForUser() {
	$('div#notifications_div').toggle();
	$("div#notifications").toggleClass("notifications_selected");
	$("div#notifications").toggleClass("fb_user_button_selected");
}
function setFooterHeight(){
    var pageHeight = window.innerHeight;
    var contentHeight = $("div#header_bar").outerHeight(true) + $("div#page_content").outerHeight(true);
    var footerHeight = $("div#footer").outerHeight(true);

    var marginBottom = pageHeight - (contentHeight + footerHeight); //this will be the margin for the bottom of div#page_content (spacing it from the footer). -1 just cause            

    var url = window.location.href;
    /*###########################################change url here######################################3*/
    if(marginBottom >= 0 || url == "http://localhost/TreeBox/index.php"){ //total page content does not require user to scroll, just display footer at bottom of page
        $('div#page_content').css("margin-bottom", marginBottom);    
    }
    else{
        //$('div#page_content').css("height", "auto");
    }
    //alert("pageHeight: " + pageHeight + " | contentHeight: " + contentHeight + " | footerHeight: " + footerHeight + " | marginBottom: " + marginBottom);
}
$(document).ready(function() {
	// Sticky header bar (see http://codepen.io/senff/pen/ayGvD for explanation)
	$('div#header_bar').addClass('original').clone().insertAfter('div#header_bar').addClass('cloned').css('position', 'fixed').css('top', '0').css('margin-top', '0').css('z-index', '500').removeClass('original').hide();
	scrollIntervalID = setInterval(stickIt, 10);

	/*************************navbar*********************/
	//calculate padding for navbar elements
	paddingNavBar = $("#navbar_menu").outerHeight(true) - ($("#navbar_menu:first-child:first-child").height()); //that crazy selector chain selects a nav bar list item's link text
	paddingNavBar = paddingNavBar / 2;
	$("ul#navbar_menu").navbar({ //see custom plugin ("js/jquery.navbar.js")
		fontFamily: 'Noto Sans Bold, sans-serif', 
		fontWeight: '400', //supports font-weight: 400 (normal) or font-weight: 700 (bold)
		fontSize: '1em',
		//letterSpacing: '.04em',
		bgColor : '#4692bb',
		color : 'white',
		//border: '1px solid #009AFF',
		hoverBgColor : '#4692bb',
		hoverColor : 'white',
		hoverBorderBottom: "4px solid black",
		//borderRadius: '.3em', //borderRadius only does the border of the whole list, not each link
		linkWidth : 'auto',
		padding : paddingNavBar
	});

	//adjust header bar height to include all elements
	$("div#header_bar").height($("#navbar_menu").outerHeight(true));

	//Drop down user option menu
	var offset = ($("div#header_bar").height() - $("div#user_profile").outerHeight(true)) / 2;
	$("div#user_options_menu_div").css("bottom", -1 * ($("div#user_options_menu_div").outerHeight(true) + (offset * 10 / 9))); //1.2 for adjustment
	$("div#user_profile").click(function(){

		$(document).click(function(event){ 
            if(!$(event.target).closest('div#user_profile').length) { //make sure the element clicked is not an ancestor of the user_profile div
                $("div#user_options_menu_div").hide();
                
                $("div#user_profile").removeClass("user_profile_selected");
                $("div#user_profile").removeClass("fb_user_button_selected");
                $("img#show_more_icon").show();
				$("img#show_more_icon_selected").hide();
            } 
        });

        toggleUserProfileOptions();
	});

	//notifications bar
	var offset = ($("div#header_bar").height() - $("div#notifications").outerHeight(true)) / 2;
	$("div#notifications_div").css("bottom", -1 * ($("div#notifications_div").outerHeight(true) + (offset * 11/9))); //sets the position of the notifications div
	$("div#notifications").click(function(){
		$(document).click(function(event){ 
            if(!$(event.target).closest('div#notifications').length) { //if user clicks outside of the notifications div
                $("div#notifications_div").hide();

                $("div#notifications").removeClass("notifications_selected");
                $("div#notifications").removeClass("fb_user_button_selected");
            } 
        });

        checkNotificationsForUser();
	});

	//this will apply to the menu lists from the user options and notifications 
	/*make sure text changes color when hovering (it misses if the cursor is just on the border of the list item)*/
	$("li.action").hover(
		function(){ //hoverIn
			$(this).find($("a.action")).css("color", "white"); //changes child a to white
		},
		function(){ //hoverOut
			$(this).find($("a.action")).css("color", "#232937");
		}
	);
}); //end document.ready()
$(window).load(function(){ //make sure that ALL items on page TRULY load 
	$("div#header_bar").height($("#navbar_menu").outerHeight(true));
	//must wait for the header bar to load before setting the footer (account for header's height)
    /**********************adjusting the footer**********************/
    setFooterHeight();
});


