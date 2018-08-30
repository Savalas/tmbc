var RetrievPasswordModel = function() {

    var self=this;
    self.new_password = ko.observable("");
    self.new_password = ko.observable("");
    self.confirm_password = ko.observable("");
    self.submittresetPassword = function() {

        error_remove();
        $(".submit-btn").addClass("loader-center btn-disabled"); $('.loading-image').show(); $(".alert-mess").hide()
        var errorMsg='';
        var userid = $("#user_id").val();
        self.userid = ko.observable(userid);
        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
            console.log(postData);
        $.post("/account/submitpassword",postData,
            function(data) {
                
                if(data.status.code=="200"){
                    
                    $('.alert-mess').fadeIn().html('<div class="alert alert-success alert-dismissable"><a class="close" data-dismiss="alert" aria-label="close">&times;</a>Password updated.</div>');
                    
                    setTimeout(function(){ window.location.href = "/account/signin"; }, 3000);
                } else{
                
                    var errors=data.status.errors;
                    var var_count = 0;
                    $.each( errors, function( key, value ) {

                        if( var_count == '0' ) $('#'+key).focus();
                        $('<div class="error-msg-field">'+value+'</div>').insertAfter($('#'+key));
                        var_count ++;
                    });
                }

                $(".submit-btn").removeClass("loader-center btn-disabled"); $('.loading-image').hide();
            });
    };

};

function error_remove () {

    $('.error-msg-field').hide().html('');
}

$(document).ready(function(){
    
    var resetpasswordForm=document.getElementById("resetpasswordForm");
    ko.applyBindings(new RetrievPasswordModel(), resetpasswordForm);
});