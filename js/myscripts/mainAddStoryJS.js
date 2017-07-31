$(window).load(function(){
    var fieldsetWidth = $("fieldset").width();
    var descriptionWidth = $("textarea#story_text").outerWidth(true);
    var spanMarginWidth = $("span.form_required").outerWidth(true) - $("span.form_required").width();
    var width = fieldsetWidth - descriptionWidth - spanMarginWidth;
    $("textarea#story_text").next("span.form_required").css("width", width);
});

$(document).ready(function() {
	/******************** MAP stuff********************/	
	var geocoder = new google.maps.Geocoder(); //auto complete in location input prompt

	/***************************add_story form*************************/
    $("#story_topic").focus(); //starts cursor here when page loads

	/*//add required *
	$(":text, #select_period, #story_text").after("<span class='form_required'>*</span>");
	$("#story_text").next().css({ //aligns the <textarea> with the <span>*</span>
		"display": "inline-block",
		"vertical-align": "top"
	});*/

    //topic autocomplete
    $("input#story_topic").autocomplete({
        source: "phpfunctions/topicAutocomplete.php",
        minLength: 1,
        open: function() {
            $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
        },
        close: function() {
            $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
        }
    });

    //location autocomplete
    var input = document.getElementById('story_loc');
    autocomplete = new google.maps.places.Autocomplete(input);

    //jQuery UI the submit button
	//$("#submit_story").button();
    $("a#submit_story").click(function(){
        $("#add_story_form").submit();
    })

	/*--------------Validate the form--------------*/
    /*
    *override the mainstyle attribute keeping .error's hidden, 
    *no error message will be shown until the user tries to submit an inappropriate form
    *the purpose is for this div to also serve as spacing b/t the legend and the form elements
    */
    $("div.error").show();

	
    var canSubmit = false;
	$("#add_story_form").submit(function(event) {
		var isValid = true;
		var errorCounter = 0; //keep track of form errors

        //Validate function
        var validateClear = function(form_element, formElementVal){ //second run of validation
            form_element.val(formElementVal);
            form_element.removeAttr("style"); //remove highlight
            form_element.next().text("");
        }
        var validateError = function(form_element, errorMessage){
            //form_element.next().text(errorMessage); //replaces <span>*</span> with the error message
            form_element.attr("style", "border: 1px solid red"); //add highlight, doing with attr() because it will be removed later on with removeAttr()
            form_element.focus(function(){
                form_element.removeAttr("style"); //remove highlight
            });
            isValid = false;
            errorCounter++;
        }
        var validateForm = function(form_element, errorMessage){
            // validate the story_headline entry
            var formElementVal = form_element.val().trim(); //sticky
            if (formElementVal == "") {
                validateError(form_element, errorMessage);
            } else {
                validateClear(form_element, formElementVal);
            }
        }

        validateForm($("#story_topic"), "Please give your story a topic")

        // validate the story_headline entry
        validateForm($("#story_headline"), "Please give your story a headline");

        // validate the story_text
        validateForm($("#story_text"), "Please enter a story");

        // validate the story_loc entry, this includes getting the individual parts of the location/address
        var storyLoc = $("#story_loc").val().trim();
        var address = "", city = "", state = "", zip = "", country = "", formattedAddress = "";
        var lat, lng;
        var locValid = true; //check if the location can be found on google maps
        if (storyLoc == "") {
            validateError($("#story_loc"), "Please give your story a location");
        } else {
            geocoder.geocode({address : storyLoc}, function(results) {
                if (!results[0]){
                    validateError($("#story_loc"), "Please enter a valid location");
                } else{
                    for (var i = 0; i < results[0].address_components.length; i++) {
                        var addr = results[0].address_components[i];
                        // check if this entry in address_components has a type of country
                        if (addr.types[0] == 'country')
                            country = addr.long_name;
                        else if (addr.types[0] == 'street_address') // address 1
                            address = address + addr.long_name;
                        else if (addr.types[0] == 'establishment')
                            address = address + addr.long_name;
                        else if (addr.types[0] == 'route')  // address 2
                            address = address + addr.long_name;
                        else if (addr.types[0] == 'postal_code') // Zip
                            zip = addr.short_name;
                        else if (addr.types[0] == ['administrative_area_level_1']) // State
                            state = addr.long_name;
                        else if (addr.types[0] == ['locality']) // City
                            city = addr.long_name;
                    }

                    if (results[0].formatted_address != null) {
                        formattedAddress = results[0].formatted_address;
                    }

                    var location = results[0].geometry.location;

                    lat = location.lat();
                    lng = location.lng();

                    //set hidden values so that PHP script can access them
                    $("#hidden_loc_lat").val(lat);
                    $("#hidden_loc_lng").val(lng);
                    $("#hidden_loc_address").val(address);
                    $("#hidden_loc_city").val(city);
                    $("#hidden_loc_state").val(state);
                    $("#hidden_loc_zip").val(zip);
                    $("#hidden_loc_country").val(country);
                    $("#hidden_loc_formatted_address").val(formattedAddress);
                    /*debugging
                    alert("Address: " + address + '\n' + 'City: '+ city + '\n' + 'State: '+ state + '\n' + 
                        'Zip: '+ zip + '\n' + 'Country: ' + country + '\n' + 'Formatted Address: '+ 
                        formattedAddress + '\n' + 'Lat: '+ lat + '\n' + 'Lng: '+ lng);*/
                    validateClear($("#story_loc"), storyLoc);
                    
                    canSubmit = true;
                    if(canSubmit && isValid){
                        $("#add_story_form").trigger('submit');
                    }
                }
            });
        }

        // prevent the default action of submitting the form if any entries are invalid 
        if (isValid == false) {
        	event.preventDefault();

        	var pluralFields = " field. It has";
        	if(errorCounter > 1)
        		pluralFields = " fields. They have";
        	var message = 'You missed ' + errorCounter + pluralFields + ' been highlighted.'
		    $("div.error span").html(message);
		    $("div.error").show().css("height", "auto");

		    errorCounter = 0; //reset error counter
        } else {
            if(!canSubmit){
                event.preventDefault();
                $("#add_story_form").unbind('submit');
            }
            $("div.error").hide();
        }
    });	// end submit
    
    //Disable press enter key to submit form if form not completely filled (annoying when trying to confirm geocoder autocomplete selection)
    function checkIfFullFunction() {
        var good = true;
        if($('input#story_headline').val().length === 0)
            good = false;
        else if($('input#story_loc').val().length === 0)
            good = false;
        else if($("textarea#story_descript").val().length === 0)
            good = false;       
        if(good)
            return true;
        else
            return false;
    }

    $(window).keydown(function(event){
        if((event.keyCode == 13) && (checkIfFullFunction() == false) && (!$("textarea").is(":focus"))) {
            event.preventDefault();
            return false;
        }
    });
}); //end document.ready()
