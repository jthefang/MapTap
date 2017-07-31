//tab controller
var LoginModalController = {
    tabsElementName: ".logmod__tabs li",
    tabElementName: ".logmod__tab",
    inputElementsName: ".logmod__form .input",
    hidePasswordName: ".hide-password",
    
    inputElements: null,
    tabsElement: null,
    tabElement: null,
    hidePassword: null,
    
    activeTab: null,
    tabSelection: 0, // 0 - first, 1 - second
    
    findElements: function () {
        var base = this;
        
        base.tabsElement = $(base.tabsElementName);
        base.tabElement = $(base.tabElementName);
        base.inputElements = $(base.inputElementsName);
        base.hidePassword = $(base.hidePasswordName);
        
        return base;
    },
    
    setState: function (state) {
    	var base = this,
            elem = null;
        
        if (!state) {
            state = 0;
        }
        
        if (base.tabsElement) {
        	elem = $(base.tabsElement[state]);
            elem.addClass("current");
            $("." + elem.attr("data-tabtar")).addClass("show");
        }
  
        return base;
    },
    
    getActiveTab: function () {
        var base = this;
        
        base.tabsElement.each(function (i, el) {
           if ($(el).hasClass("current")) {
               base.activeTab = $(el);
           }
        });
        
        return base;
    },
   
    addClickEvents: function () {
    	var base = this;
        
        base.hidePassword.on("click", function (e) {
            var $this = $(this),
                $pwInput = $this.prev("input");
            
            if ($pwInput.attr("type") == "password") {
                $pwInput.attr("type", "text");
                $this.text("Hide");
            } else {
                $pwInput.attr("type", "password");
                $this.text("Show");
            }
        });
 
        base.tabsElement.on("click", function (e) {
            var targetTab = $(this).attr("data-tabtar");
            
            e.preventDefault();
            base.activeTab.removeClass("current");
            base.activeTab = $(this);
            base.activeTab.addClass("current");
            
            base.tabElement.each(function (i, el) {
                el = $(el);
                el.removeClass("show");
                if (el.hasClass(targetTab)) {
                    el.addClass("show");
                }
            });
        });
        
        base.inputElements.find("label").on("click", function (e) {
           var $this = $(this),
               $input = $this.next("input");
            
            $input.focus();
        });
        
        return base;
    },
    
    initialize: function () {
        var base = this;
        
        base.findElements().setState().getActiveTab().addClickEvents();
    }
};
function validateEmail(email){
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,6})?$/;
    return emailReg.test(email);
}
function validatePassword(password){
    if(password.length < 8){
        return false;
    }
    return true;
}
function validate(email, password){
    var html = "";
    var email_valid = true;
    if(email.trim() == '' || password.trim() == ''){
        //alert("empty fields");
        var email_empty = false;
        if(email ==''){ 
            html = "Please enter your email"; 
            email_empty = true;
        }

        if(password == ''){                
            if(email_empty){
                html += " and password"; 
            }else{
                html = "Please enter your password";    
            }
        }
    }else if(!validateEmail(email)){
        html = "Please enter a valid email";
        email_valid = false;
    }else if(!validatePassword(password)){
        if(email_valid){
            html = "Passwords are 8-30 characters long";
        }else{
            html += " and password"
        }
    }else{
        html = "no problem";
    }
        
    return html;
}
function login(email, password){
    $.ajax({
        url: "phpfunctions/login.php",
        type: "GET",
        data: "email=" + email + "&password=" + password,
        dataType: "json",
        error: function(xhr, status, error) {
            console.log("Error: logIn() " + xhr.status + " - " + error);
        },
        success: function(data) {
            //alert("success, email = " + email + " and password = " + password);
            if(data.error == "none"){
                //alert("no error");
                 /*encoded like:
                {
                    "error" : some error message,
                    "redirectUrl" : redirect (only if query was sucessful, ie. error='none')
                }
                */
                window.location = data.redirectUrl;
            } else{ //there was an error
                //alert("error : " + data.error);
                var error_msg = data.error;
                $("div#login_err").html(error_msg).css("color", "red"); 
            }//end if no error w/ signing   
        } //end success for getting all the markers
    }); //end ajax for signing in 
}
$(document).ready(function() {
	LoginModalController.initialize();

    /*if(event.keyCode == 13){ //if enter key is pressed
        $("button#login").trigger("click");
    }*/
    $('div#login').keypress(function (e) {
        var key = e.which;
        if(key == 13){   // the enter key code
            $('button#login').click();
            return false;  
        }
    }); 
    $('div#signup').keypress(function (e) {
        var key = e.which;
        if(key == 13){   // the enter key code
            $('button#signup').click();
            return false;  
        }
    });  
    $("button#login").click(function(){
        //alert("button clicked!");
        var email = $("input#login_email").val();
        var password = $("input#login_password").val();
        // Checking for blank fields.
        var valid = validate(email, password, false);
        if(valid != "no problem"){
            $("div#login_err").html(valid).css("color", "red"); 
        } else { //valid email and password
            //alert("valid");
            login(email, password);
        } //end if email and password are valid
    }); //end on login button click
    
    $("button#signup").click(function(){
        var email = $("input#signup_email").val();
        var password = $("input#signup_password").val();
        var confirm = $("input#confirm_password").val();
        var firstName = $("input#signup_firstname").val();
        var lastName = $("input#signup_lastname").val();

        var valid = validate(email, password, true);
        //alert("submit form pressed");
        if(valid != "no problem"){
            //alert("problem with validation");
            $("div#signup_err").html(valid).css("color", "red"); 
        }else if(confirm != password){
            //alert("passwords dont match");
            var html = "Passwords do not match";
            $("div#signup_err").html(html).css("color", "red");
        }else if(firstName.trim() == ""){
            //alert("please enter a first name");
            var html = "Please enter your first name";
            $("div#signup_err").html(html).css("color", "red");
        }else if(!grecaptcha.getResponse()){
            var html = "Please verify your humanness";
            $("div#signup_err").html(html).css("color", "red");
        }else{ //valid email and password
            //alert("valid");
            $.ajax({
                url: "phpfunctions/signup.php",
                type: "GET",
                data: "email=" + email + "&password=" + password + "&firstname=" + firstName + "&lastname=" + lastName,
                dataType: "json",
                error: function(xhr, status, error) {
                    console.log("Error: signup() " + xhr.status + " - " + error);
                },
                success: function(data) {
                    //alert("success, email = " + email + " and password = " + password);
                    if(data.success == true){
                        //alert("no error");
                        login(email, password);
                    } else{ //there was an error
                        //alert("error : " + data.error);
                        var error_msg = data.error;
                        $("div#signup_err").html(error_msg).css("color", "red"); 
                    }//end if no error w/ signing   
                } //end success for getting all the markers
            }); //end ajax for signing in 
        } //end if email and password are valid
    }); //end of signup button click
});