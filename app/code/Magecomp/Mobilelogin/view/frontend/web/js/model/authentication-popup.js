define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    return {
        modalWindow: null,
        
        /** Show login popup window */
        showModal: function () {
        
            var layout = $("#layout").val();
            var options = {
                'type': 'popup',
                'modalClass' : 'customer-login-popup '+ layout,
                'focus': '[name=username]',
                'responsive': true,
                'innerScroll': true,
                'trigger': '.proceed-to-checkout',
                'buttons': []
            };
            modal(options, $('#customer-popup-login'));
            $("#customer-popup-login").modal('openModal');
            $("#customer-popup-login").addClass("ultimate")
            $(".registratio-section").hide();
            $(".forgot-password-section").hide();
            $(".login-section").show();
            $(".forgotimage").hide();
            $(".regimage").hide();
            $(".loginimage").show();
        }
    };
});