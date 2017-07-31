$(document).ready(function() {
    /******************** MAP stuff********************/    
    var geocoder = new google.maps.Geocoder(); //auto complete in location input prompt

    /***************************edit_project form*************************/
    $("#project_name").focus();

    //add the required sign "*"
    $(":text, #select_period, #project_description").after("<span class='form_required'>*</span>");
    $("#project_description").next().css({ //aligns the <textarea> with the <span>*</span>
        "display": "inline-block",
        "vertical-align": "top"
    });

    var input = document.getElementById('project_loc');
    autocomplete = new google.maps.places.Autocomplete(input);

    /*jQuery UI styling*/
    $("button#delete_project").button(); 
    if ($('#project_date').length) { //if project_date edit element exists, won't exist if project is expired
        $("#project_date").datepicker({
            showOn: "button",
            buttonImage: "images/calendar.gif",
            buttonImageOnly: true,
            buttonText: "Select date",
            minDate: new Date(), //sets the minimum date to today with a new Date object
        });
    }

    /*-----------submit button-----------------*/
    $("a#submit_project").click(function(){
        $("#edit_project_form").submit();
    })

    /*--------------Validate the form--------------*/
    /*
    *override the mainstyle attribute keeping .error's hidden, 
    *no error message will be shown until the user tries to submit an inappropriate form
    *the purpose is for this div to also serve as spacing b/t the legend and the form elements
    */
    $("div.error").show(); 
    
    var canSubmit = false;
    $("#edit_project_form").submit(function(event) {
        var isValid = true;
        var errorCounter = 0; //keep track of form errors

        //Validate function
        var validateClear = function(form_element, formElementVal){
            form_element.val(formElementVal);
            form_element.removeAttr("style"); //remove highlight
            form_element.next().text("");
        }
        var validateError = function(form_element, errorMessage){
            form_element.next().text(errorMessage); //replaces <span>*</span> with the error message
            form_element.attr("style", "border: 1px solid red"); //add highlight, doing with attr() because it will be removed later on with removeAttr()
            isValid = false;
            errorCounter++;
        }
        var validateForm = function(form_element, errorMessage){
            // validate the project_name entry
            var formElementVal = form_element.val().trim();
            if (formElementVal == "") {
                validateError(form_element, errorMessage);
            } else {
                validateClear(form_element, formElementVal);
            }
        }

        // validate the project_name entry
        validateForm($("#project_name"), "Please give your project a name.");

        // validate the project_description
        validateForm($("#project_description"), "Please give your project a description.");

        // validate the project_loc entry, this includes getting the individual parts of the location/address
        var projectLoc = $("#project_loc").val().trim();
        var address = "", city = "", state = "", zip = "", country = "", formattedAddress = "";
        var lat, lng;
        var locValid = true; //check if the location can be found on google maps
        if (projectLoc == "") {
            validateError($("#project_loc"), "Please give your project a location.");
        } else {
            geocoder.geocode({address : projectLoc}, function(results) {
                if (!results[0]){
                    validateError($("#project_loc"), "Please enter a valid location.");
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
                    validateClear($("#project_loc"), projectLoc);
                    
                    canSubmit = true;
                    if(canSubmit && isValid){
                        $("#edit_project_form").trigger('submit');
                    }
                }
            });
        }

        if ($('#project_date').length) { //if project_date edit element exists, won't if project is expired
            // validate the project_date entry
            var datePattern = /^((((0[13578])|([13578])|(1[02]))[\/](([1-9])|([0-2][0-9])|(3[01])))|(((0[469])|([469])|(11))[\/](([1-9])|([0-2][0-9])|(30)))|((2|02)[\/](([1-9])|([0-2][0-9]))))[\/]\d{4}$|^\d{4}$/;
            var projectDate = $("#project_date").val().trim();
            if (projectDate == "") {
                $("#project_date").next().next().text("Please enter the date of your project."); //need double next()'s because of neighbor calendar img
                $("#project_date").attr("style", "border: 1px solid red");
                isValid = false;
                errorCounter++;
            } else if(!datePattern.test(projectDate)) { //make sure valid date format
                $("#project_date").next().next().text("Please enter a valid date (mm/dd/yyyy).");
                $("#project_date").attr("style", "border: 1px solid red");
                isValid = false;
                errorCounter++;
            } else {
                $("#project_date").val(projectDate); //sticky
                $("#project_date").removeAttr("style"); //remove highlight
                $("#project_date").next().next().text("");
            }
        }

        // prevent the default action of submitting the form if any entries are invalid 
        if (isValid == false) {
            event.preventDefault();

            var pluralFields = " field. It has";
            if(errorCounter > 1)
                pluralFields = " fields. They have";
            var message = 'You missed ' + errorCounter + pluralFields + ' been highlighted'
            $("div.error span").html(message);
            $("div.error").show();

            errorCounter = 0; //reset error counter
        } else {
            if(!canSubmit){
                event.preventDefault();
                $("#edit_project_form").unbind('submit');
            }
            $("div.error").hide();
        }
    }); // end submit
}); //end document.ready()
