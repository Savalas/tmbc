var SigninViewModel = function() {
    var self=this;
    self.usernameEmail = ko.observable("");
    self.password= ko.observable("");
    self.remeberme = ko.observable(true);
    self.submitSignin = function() {

        error_remove(); 
        $(".signin-btn").addClass("loader-center btn-disabled"); $('.loading-image').show(); $(".alert-mess-signin").hide();
        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
        $.post("/account/requestsignin",postData,
            function(data) {
                
                $("#loginNotif").html("");
                if(data.status.code=="400"){

                    window.location.href = "/device/add";
                }
                if(data.status.code=="200"){

                    if(window.top==window) { 
                    
                        // you're not in a frame so you reload the site
                        location.reload(); //reloads after 3 seconds
                    } else {
                        //you're inside a frame, so you stop reloading
                    }

                } else{
                    var errorMsg='';
                    var errors=data.status.errors;
                    var var_count = 0;
                    $.each( errors, function( key, value ) {

                        if ( key == "loginDetails" ) {
                            
                            $('.alert-mess-signin').fadeIn().html('<div class="alert alert-danger"><a class="alert-link">'+value+'</a></div>');

                        } else {
                            
                            if( var_count == '0' ) $('#'+key).focus();
                            $('<div class="error-msg-field">'+value+'</div>').insertAfter($('#'+key));
                        }
                        var_count ++;
                    });
                }

                $(".signin-btn").removeClass("loader-center btn-disabled"); $('.loading-image').hide();
            });
    };
};

var SignupViewModel = function() {

    var self=this;
    self.usernameEmail = ko.observable("");
    self.password= ko.observable("");
    self.companyName = ko.observable("");
    self.channelName = ko.observable("");
    self.agreement = ko.observable(false);
    
    self.submitSignup = function() {

        error_remove();
        var errorMsg='';
        $(".signup-btn").addClass("loader-center btn-disabled"); $('.loading-image').show(); $(".alert-mess").hide();
        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
        $.post("/account/requestsignup",postData,
            function(data) {

                $("#status_signup").html("");
                if(data.status.code=="400"){

                    window.location.href = "/device/add";
                }
                if(data.status.code=="200"){
                    $("#status_signup").html("&nbsp; <span style='color:blue'>Logging In ...<img src='/skin/images/smallSpinner.gif'/></span>");
                    if(window.top==window) {

                        // you're not in a frame so you reload the site
                        location.reload(); //reloads after 3 seconds
                    } 
                }
                else{
                
                    var errors=data.status.errors;
                    var var_count = 0;
                    $.each( errors, function( key, value ) {
                        
                        if( var_count == '0' ) $('#'+key).focus();
                        $('<div class="error-msg-field">'+value+'</div>').insertAfter($('#'+key));
                        var_count ++;
                    });
                }

                $(".signup-btn").removeClass("loader-center btn-disabled"); $('.loading-image').hide();
            });
    };

};
var forgotPasswordViewModel = function() {
    
    var self=this;
    self.check_username = ko.observable(false);
    self.check_password = ko.observable(false);
    self.email_forgot = ko.observable("");

    self.submitForgotPassword = function() {

        error_remove();
        var errorMsg='';
        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
        
        $(".forgot-btn").addClass("loader-center btn-disabled"); $('.loading-image').show(); $(".alert-mess").hide();
        $.post("/account/requestforgotpass",postData,
            function(data) {
                $("#status_forgot_password").html("");
                if(data.status.code=="200"){
                    
                    $('.alert-mess').fadeIn().html('<div class="alert alert-success alert-dismissable">Please check your email\'s inbox for more instructions.</div>');
                } else{

                    var errors=data.status.errors;
                    var var_count = 0;
                    $.each( errors, function( key, value ) {
                        if( var_count == '0' ) $('#'+key).focus();
                        $('<div class="error-msg-field">'+value+'</div>').insertAfter($('#'+key));
                        var_count ++;
                    });
                }

                $(".forgot-btn").removeClass("loader-center btn-disabled"); $('.loading-image').hide();
            });
    };

};


// This is called with the results from from FB.getLoginStatus().
function statusChangeCallback(response) {
    /*console.log('statusChangeCallback');
    console.log(response);*/
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected') {
        // Logged into your app and Facebook.
        testAPI();
    } else if (response.status === 'not_authorized') {
        // The person is logged into Facebook, but not your app.

    } else {
        // The person is not logged into Facebook, so we're not sure if
        // they are logged into this app or not.
    }
}

// This function is called when someone finishes with the Login
// Button.  See the onlogin handler attached to it in the sample
// code below.


function login(){
    var urlRef=$("#fbLogin").attr("url");
    document.location.href=urlRef;
}

function error_remove () {

    $('.error-msg-field').hide().html('');
}

// Here we run a very simple test of the Graph API after login is
// successful.  See statusChangeCallback() for when this call is made.
function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
        console.log('Successful login for: ' + response.name);
    });
}
$(document).ready(function(){
    $("#status_signin").hide();
    $("#status_signup").hide();
    $("#status_forgot_password").hide();

    $("#fbLogin").click(function() {
        FB.login(function(response) {
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
                login();
            } else if (response.status === 'not_authorized') {
                // The person is logged into Facebook, but not your app.
                login();
            } else {
                // The person is not logged into Facebook, so we're not sure if
                // they are logged into this app or not.
            }

        }, {scope: 'email,publish_actions,user_friends'});
    });

    var signInForm=document.getElementById("signinForm");
    var signUpForm=document.getElementById("signupForm");
    var forgotPasswordForm=document.getElementById("forgotPasswordForm");

    ko.applyBindings(new SigninViewModel(), signInForm);
    ko.applyBindings(new SignupViewModel(), signUpForm);
    ko.applyBindings(new forgotPasswordViewModel(), forgotPasswordForm);

    window.fbAsyncInit = function() {
        FB.init({
            appId      : '1057029451012693',
            cookie     : true,  // enable cookies to allow the server to access
                                // the session
            xfbml      : true,  // parse social plugins on this page
            version    : 'v2.5' // use graph api version 2.5
        });

        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });

    };
    // Load the SDK asynchronously
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    $('#inner-content-div').slimScroll({
        height: '500px'
    });

});
