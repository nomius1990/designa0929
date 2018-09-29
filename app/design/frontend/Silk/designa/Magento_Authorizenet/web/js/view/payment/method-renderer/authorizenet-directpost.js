define([
    'jquery',
    'Magento_Payment/js/view/payment/iframe',
    'mage/translate'
],
function ($, Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_Authorizenet/payment/authorizenet-directpost',
            timeoutMessage: $t('Sorry, but something went wrong. Please contact the seller.')
        },
        placeOrderHandler: null,
        validateHandler: null,

        setPlaceOrderHandler: function (handler) {
            this.placeOrderHandler = handler;
        },

        setValidateHandler: function (handler) {
            this.validateHandler = handler;
        },

        context: function () {
            return this;
        },

        isShowLegend: function () {
            return true;
        },

        getCode: function () {
            return 'authorizenet_directpost';
        },

        getImageSrc: function () {
            return window.checkoutConfig.payment.authorizeUrl;
        },

        isActive: function () {
            return true;
        }
    });
});
