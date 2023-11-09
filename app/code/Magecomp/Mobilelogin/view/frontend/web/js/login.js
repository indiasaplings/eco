define([
    "jquery",
    'Magento_Ui/js/modal/modal',
    "mage/template",
    'mage/translate',
    "mage/mage",
    'text!Magecomp_Mobilelogin/templates/modal/modal-mobilelogin.html'
], function($,modal,mageTemplate,$t) {
    'use strict';
    $.widget('magecomp.login', {
        
        options: {
            login: '#customer-popup-login',
            nextRegister: '#customer-popup-registration',
            prevLogin: '#customer-popup-sign-in',
            nextForgotPassword: '#customer-popup-forgot-password',
        },

        _create: function () {

            var self = this,
            authenticationOptions = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: this.options.popupTitle,
                buttons: false,
                modalClass : 'customer-login-popup ' + this.options.customClass
            };
            
            $('body').on('click','.closediv .btn-action-close', function() {
                $( ".action-close" ).trigger( "click" );
            });

            $('body').on('click', '.panel.header .ajax-login-link, .nav-sections-item-content .header .ajax-login-link', function() {
                self._resetForms();
                self._showLogin();
                modal(authenticationOptions, $(self.options.login));
                $(self.options.login).modal('openModal');
                self._setPopupTitle("Login");
                return false;
            });

        jQuery("#customerloginsubmit").click(function (e) {
        e.preventDefault();
        jQuery("#customer-login-please-wait").css('display','block');
        jQuery(".primary").css('display','none');
        jQuery(".emailpasswrong").css('display','none');
        jQuery.ajax({
            url: jQuery('#customer-login-form').attr('action'),
            type: 'post',
            data: jQuery('#customer-login-form').serialize(),
            xhrFields: {
                withCredentials: true
            },
            create: function (response) {
                var t = response.transport;
                t.setRequestHeader = t.setRequestHeader.wrap(function (original, k, v) {
                    if (/^(accept|accept-language|content-language|cookie|access-control-allow-origin|access-control-allow-headers|access-control-allow-credentials)$/i.test(k))
                        return original(k, v);
                    if (/^content-type$/i.test(k) &&
                        /^(application\/x-www-form-urlencoded|multipart\/form-data|text\/plain)(;.+)?$/i.test(v))
                        return original(k, v);
                    return;
                });
            },
            success: function (response) {
                if(response.error){
                    jQuery(".emailpasswrong").css('display','block');
                    jQuery(".emailpasswrong span").text(response.message);
                    jQuery('#customer-login-please-wait').css('display','none');
                    jQuery(".primary").css('display','block');
                    jQuery("#loginsubmit").css('display','block');
                    return;
                }

                if (response.redirect) {
                    document.location = response.redirect;
                    return;
                }
            }
        });
    });

        jQuery(".regi-sendotp").click(function (e) {

        var regimobile = jQuery("#mobile-mobileget").val();
        var url = jQuery(".setdotpurl").val();
        jQuery(".blankerror").css('display','none');
        jQuery(".mobileNotValid").css('display','none');
        jQuery(".mobileotpsenderror").css('display','none');
        jQuery(".mobileExist").css('display','none');

        jQuery(".resend").css('display','none');
        jQuery(".sending").css('display','block');
        


        if(!regimobile){
            jQuery(".blankerror").css('display','block');

            return false;
        }
        if(validateMobile(regimobile) == false){
            jQuery(".mobileNotValid").css('display','block');
            return false;
        }
        var countrycode=jQuery('#create-account-page #countryreg').val().replace('+','');
        var mobile=countrycode.concat(regimobile);

        jQuery(".sendotp").css('display','none');
        jQuery("#reg-sms-please-wait").css('display','block');
        jQuery(this).prop('disabled',true);
        jQuery.ajax({
            url: url,
            type:'GET',
            data:{mobile:mobile},
            success: function (data) {
                jQuery(".sendotp").css('display','block');
                jQuery("#reg-sms-please-wait").css('display','none');

            
                if(data.status === true){
      
                    jQuery("#otpverify2").css('display','block');
                    jQuery("#createmobile").val(mobile);
                    jQuery(".mobileget").css('display','block');
                    jQuery(".regi-sendotp").css('display','none');
                    document.getElementById("mobile-mobileget").readOnly = true;
                    jQuery("#otpverify2").css('display','block');
                    jQuery(".resend").css('display','block');
                    jQuery(".sending").css('display','none');
                    jQuery("#regi-mobilenumber").val(mobile);

                }else if(data.status =="exist"){
                    jQuery(".mobileExist").css('display','block');
                }else{
                    jQuery(".mobileotpsenderror").css('display','block');
                }
                jQuery('.regi-sendotp').prop('disabled',false);
            },
            error: function () {

                jQuery(".sendotp").css('display','block');
                jQuery("#reg-sms-please-wait").css('display','none');
                jQuery(this).prop('disabled',false);
            }
        });

    });
    jQuery(".create-account-resend-otp").click(function (e) {
        jQuery(".regi-sendotp").trigger('click');
        jQuery("#reg-sms-please-wait").css('display','none');
    });
        function validateMobile(mobile)
    {
        var filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;

        if (filter.test(mobile)) {
            if(mobile.length >= 10 && mobile.length <= 13){
                var validate = true;
            } else {
                var validate = false;
            }
        }
        else {
            var validate = false;
        }
        return validate;
    }

    jQuery(".mobileverifyotp").click(function (e) {
        var otp =  jQuery("#mobile-otp").val();
        var mobile = jQuery("#mobile-mobileget").val();
        jQuery(".blankotperror").css('display','none');
        jQuery(".error").css('display','none');
        if(isBlank(otp) == false){
            jQuery(".blankotperror").css('display','block');
            return false;
        }
        var countrycode=jQuery('#create-account-page #countryreg').val().replace('+','');
        mobile=countrycode.concat(mobile);
        jQuery(".checkotperror").css('display','none');
        jQuery("#reg-otp-verify-please-wait").css('display','block');
        jQuery(".verifyotp").css('display','none');
        jQuery(this).prop('disabled',true);
        jQuery.ajax({
            url: jQuery(".checkotpurl").val(),
            type: 'GET',
            data:{otp:otp,mobile:mobile},
            success: function (data) {
                jQuery(".verifyotp").css('display','block');
                jQuery("#reg-otp-verify-please-wait").css('display','none');
                if(data == 'true'){
                    jQuery("#createotp").val(otp);
                    jQuery(".otpverify").css('display','none');
                    jQuery(".registraionform").css('display','block');
                    jQuery(".submit").prop('disabled', false);

                }else{
                    jQuery(".checkotperror").css('display','block');
                }
                jQuery(".blankotperror").css('display','none');
                jQuery('.mobileverifyotp').prop('disabled',false);
            },
            error: function () {
                jQuery("#reg-otp-verify-please-wait").css('display','none');
                jQuery(".verifyotp").css('display','block');
                jQuery(this).prop('disabled',false);
            }
        });
    });

    function isBlank(value)
    {
        if(!value)
        {
            return false;
        }
    }
     $(document).ready(function (e) {
        $(".form-create-account .submit").attr('disabled', 'disabled');
    });
            $('body').on('click', '.panel.header .ajax-register-link, .nav-sections-item-content .header .ajax-register-link', function() {
                self._resetForms();
                self._showRegistration();
                modal(authenticationOptions, $(self.options.login));
                $(self.options.login).modal('openModal');
                self._setPopupTitle("Register");
                return false;
            });
            $('body').on('click', self.options.nextRegister, function() {
                self._setPopupTitle("Register");
                self._showRegistration();
                return false;
            });
            $('body').on('click', self.options.prevLogin, function() {
                self._setPopupTitle("Login");
                self._showLogin();
                return false;
            });
            $('body').on('click', self.options.nextForgotPassword, function() {
                self._setPopupTitle("Forgot Password");
                self._showForgotPassword();
                return false;
            });


            $("body").on("click",".customer-popup-sign-in", function() {
                $(self.options.prevLogin).trigger("click");
            });
            $('body').on('click','.login-option #loginwithotp', function() {
                $(".password-login").hide();
                $(".otp-login-verify").hide();
                $(".otp-login").show();

                $("#loginwithajax").removeClass("active");
                $("#loginwithotp").addClass("active");
            });
            $('body').on('click','.login-option #loginwithajax', function() {
                $(".otp-login").hide();
                $(".otp-login-verify").hide();
                $(".password-login").show();

                $("#loginwithotp").removeClass("active");
                $("#loginwithajax").addClass("active");
            });

            $('body').on('change',"input[type=radio][name=reset-type]", function(){
                var seletedType = this.value;
                if(seletedType == "mobile") {
                    $(".emailforgot").hide();
                    $(".mobileforgot").show();
                } else {
                    $(".mobileforgot").hide();
                    $(".mobileforgotpassword").hide();
                    $(".mobileforgotverify").hide();
                    $(".emailforgot").show();
                }
            });



            $('body').on('click',".registraiontpsend button", function(e) {
                self._ajaxRegistrationOtpSend(e);
            });
            $('body').on('click',".registraiontpverify button", function(e) {
                self._ajaxRegistrationOtpVerify(e);
            });
            $('body').on('click',".registraionform button", function(e) {
                self._ajaxRegistrationCreate(e);
            });
            $('body').on('click',".password-login button", function(e) {
                self._ajaxLogin(e);
            });
            $('body').on('click',".otp-login button", function(e) {
                self._ajaxLoginOtpSend(e);
            });
            $('body').on('click',".otp-login-verify button", function(e) {
                self._ajaxLoginOtpVerify(e);
            });
            $('body').on('click',".mobileforgot button", function(e) {
                self._ajaxForgotOtpSend(e);
            });
            $('body').on('click',".mobileforgotverify button", function(e) {
                self._ajaxForgotOtpVerify(e);
            });
            $('body').on('click',".mobileforgotpassword button", function(e) {
                self._ajaxForgotReset(e);
            });
            $('body').on('click',".emailforgot button", function(e) {
                self._ajaxForgotEmail(e);
            });
            $('body').on('click',".update-mobile-section button", function(e) {
                self._ajaxUpdateMobile(e);
            });
        },


        _resetForms: function() {
            $(".registraion-otp-send").get(0).reset();
            $(".registraion-otp-verify").get(0).reset();
            $(".registraion-create-account").get(0).reset();
            $(".login-email").get(0).reset();
            $(".login-otp").get(0).reset();
            $(".loginotp-verify").get(0).reset();
            $(".forgot-otp").get(0).reset();
            $(".forgot-otp-veify").get(0).reset();
            $(".forgot-email").get(0).reset();
            
            $(".customer-login-popup .messages").empty();
        },

        _setPopupTitle: function(title) {
            $('.customer-login-popup .modal-title').text($.mage.__(title));
        },

       _showLogin: function() {
            
            $(".login-option #loginwithotp").trigger("click");

            $(".registratio-section").hide();
            $(".forgot-password-section").hide();
            $(".login-section").show();
            $(".forgotimage").hide();
            $(".regimage").hide();
            $(".loginimage").show();
        },

        _showRegistration: function() {
            
            $(".login-section").hide();
            $(".forgot-password-section").hide();

            $(".registraiontpverify").hide();
            $(".registraionform").hide();
            $(".registraiontpsend").show();
            $(".registratio-section").show();
             $(".forgotimage").hide();
            $(".regimage").show();
            $(".loginimage").hide();
        },

        _showForgotPassword: function() {
            $('.reset_type').val('mobile').trigger('change');

            $(".login-section").hide();
            $(".registratio-section").hide();
            $(".mobileforgotverify").hide();
            $(".mobileforgotpassword").hide();
            $(".emailforgot").hide();
            $(".mobileforgot").show();
            $(".forgot-password-section").show();
            $(".forgotimage").show();
            $(".regimage").hide();
            $(".loginimage").hide();
        },


        _ajaxRegistrationOtpSend: function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#registraion-otp-send');

            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid'); //validates form and returns boolean
            var mobile = jQuery("#registermob").val();
            var countrycode=jQuery('#countryreg').val().replace('+','');
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: {mobile:mobile},
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".registraiontpsend .messages .message").remove();
                        if(response.status == true) {
                            $(".registraiontpverify #verifymobile").val($(".registraiontpsend #registermob").val());
                            $(".registraiontpsend").hide();
                            $(".registraiontpverify").show();
                        } else {
                            $('<div/>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".registraiontpsend .messages");
                            $('<div/>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".registraiontpsend .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxRegistrationOtpVerify: function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#registraion-otp-verify');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            var otp = jQuery(".registraiontpverify #verifyotp").val();
            var mobile = jQuery("#registermob").val();
            var countrycode=jQuery('#countryreg').val().replace('+','');
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data:{verifyotp:otp,mobile:mobile},
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".registraiontpverify .messages .message").remove();
                        if(response.status == true) {
                            $(".registraionform #createmobile").val($(".registraiontpverify #verifymobile").val());
                            $(".registraionform #otp").val($(".registraiontpverify #verifyotp").val());
                            $(".registraiontpverify").hide();
                            $(".registraionform").show();
                        } else {
                            $('<div/>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".registraiontpverify .messages");
                            $('<div/>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".registraiontpverify .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxRegistrationCreate: function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#registraion-create-account');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            var mobile = jQuery("#registermob").val();
            var countrycode=jQuery('#countryreg').val().replace('+','');
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#registraion-create-account').serialize() + '&mobilenumber='+mobile,
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".registraionform .messages .message").remove();
                        if(response.status == true) {
                            if(response.redirectUrl == "" || response.redirectUrl == null)
                                location.reload(true);
                            else
                                window.location.href = response.redirectUrl;
                        } else {
                            $('<div/>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".registraionform .messages");
                            $('<div/>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".registraionform .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxLogin: function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#login-email');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#login-email').serialize(),
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".password-login .messages .message").remove();
                        if(response.status == true) {
                            if(response.redirectUrl == "" || response.redirectUrl == null)
                                location.reload(true);
                            else 
                                window.location.href = response.redirectUrl;
                        } else {
                            $('<div/>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".password-login .messages");
                            $('<div/>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".password-login .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxLoginOtpSend: function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#login-otp');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid'); //validates form and returns boolean
            var mobile = jQuery(".otp-login #loginotpmob").val();
            var countrycode=jQuery('#countryreg').val().replace('+','');        
            mobile=countrycode.concat(mobile);


            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#login-otp').serialize()+ '&loginotpmob='+mobile,
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".otp-login .messages .message").remove();
                        if(response.status == true) {
                            $(".otp-login-verify #verifymobile").val($(".otp-login #loginotpmob").val());
                            $(".otp-login").hide();
                            $(".otp-login-verify").show();
                        } else {
                            $('<div/>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".otp-login .messages");
                            $('<div/>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".otp-login .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxLoginOtpVerify: function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#loginotp-verify');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            var mobile = jQuery(".otp-login #loginotpmob").val();
            var countrycode=jQuery('#countryreg').val().replace('+','');        
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#loginotp-verify').serialize()+ '&mobile='+mobile,
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".otp-login-verify .messages .message").remove();
                        if(response.status == true) {
                            if(response.redirectUrl == "" || response.redirectUrl == null)
                                location.reload(true);
                            else
                                window.location.href = response.redirectUrl;
                        } else {
                            $('<div/>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".otp-login-verify .messages");
                            $('<div/>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".otp-login-verify .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxForgotOtpSend: function(e)
        {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#forgot-otp');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            var mobile = jQuery(".mobileforgot #forgotmob").val();
            var countrycode=jQuery('#countryreg').val().replace('+','');        
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#forgot-otp').serialize()+'&forgotmob='+mobile,
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".mobileforgot .messages .message").remove();
                        if(response.status == true) {
                            $(".mobileforgotverify #verifymobile").val($(".mobileforgot #forgotmob").val());
                            $(".mobileforgot").hide();
                            $(".mobileforgotverify").show();
                        } else {
                            $('<div/>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".mobileforgot .messages");
                            $('<div/>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".mobileforgot .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxForgotOtpVerify: function(e)
        {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#forgot-otp-veify');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            var mobile = jQuery(".mobileforgot #forgotmob").val();
            var countrycode=jQuery('#countryreg').val().replace('+','');        
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#forgot-otp-veify').serialize()+ '&mobile='+mobile,
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".mobileforgot .messages .message").remove();
                        if(response.status == true) {
                            $(".mobileforgotpassword #mobilenumber").val($(".mobileforgotverify #verifymobile").val());
                            $(".mobileforgotpassword #otp").val($(".mobileforgotverify #verifyotp").val());

                            $(".mobileforgotverify").hide();
                            $(".mobileforgotpassword").show();
                        } else {
                            $('<div/>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".mobileforgot .messages");
                            $('<div/>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".mobileforgot .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxForgotReset: function(e)
        {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#forgot-otp-password');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            var mobile = jQuery(".mobileforgot #forgotmob").val();
            var countrycode=jQuery('#countryreg').val().replace('+','');        
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#forgot-otp-password').serialize()+'&mobile='+mobile,
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".mobileforgotpassword .messages .message").remove();
                        if(response.status == true) {
                            $("#customer-popup-sign-in").trigger("click");
                            $(".login-option #loginwithajax").trigger("click");
                        } else {
                            $('<div/>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".mobileforgotpassword .messages");
                            $('<div/>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".mobileforgotpassword .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxForgotEmail: function(e)
        {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#forgot-email');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#forgot-email').serialize(),
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".emailforgot .messages .message").remove();
                        if(response.status == true) {
                            $('<div/>', {
                                "class": 'message-success success message',
                                html: ""
                            }).appendTo(".emailforgot .messages");
                            $('<div/>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".emailforgot .messages .message-success");
                        } else {
                            $('<div/>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".emailforgot .messages");
                            $('<div/>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".emailforgot .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxUpdateMobile: function(e)
        {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#update-mobile');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#update-mobile').serialize(),
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".update-mobile-section .messages .message").remove();
                        if(response.status == true) {
                            $('<div/>', {
                                "class": 'message-success success message',
                                html: ""
                            }).appendTo(".update-mobile-section .messages");
                            $('<div/>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".update-mobile-section .messages .message-success");
                        } else {
                            $('<div/>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".update-mobile-section .messages");
                            $('<div/>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".update-mobile-section .messages .message-error");
                        }
                    }
                });
            }
        }
    });
    return $.magecomp.login;
});