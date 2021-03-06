define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Paypal/js/action/set-payment-method',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/customer-data'
], function ($, Component, setPaymentMethodAction, additionalValidators, quote, customerData) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_Paypal/payment/paypal-express-bml',
            billingAgreement: ''
        },

        initObservable: function () {
            this._super()
                .observe('billingAgreement');

            return this;
        },

        showAcceptanceWindow: function (data, event) {
            window.open(
                $(event.currentTarget).attr('href'),
                'olcwhatispaypal',
                'toolbar=no, location=no,' +
                ' directories=no, status=no,' +
                ' menubar=no, scrollbars=yes,' +
                ' resizable=yes, ,left=0,' +
                ' top=0, width=400, height=350'
            );

            return false;
        },

        getPaymentAcceptanceMarkHref: function () {
            return window.checkoutConfig.payment.paypalExpress.paymentAcceptanceMarkHref;
        },

        getPaymentAcceptanceMarkSrc: function () {
            return window.checkoutConfig.payment.paypalExpress.paymentAcceptanceMarkSrc;
        },

        getPaypalSrc: function () {
            return window.checkoutConfig.payment.paypalUrl;
        },

        getBillingAgreementCode: function () {
            return window.checkoutConfig.payment.paypalExpress.billingAgreementCode[this.item.method];
        },

        getData: function () {
            var parent = this._super(),
                additionalData = null;

            if (this.getBillingAgreementCode()) {
                additionalData = {};
                additionalData[this.getBillingAgreementCode()] = this.billingAgreement();
            }

            return $.extend(true, parent, {
                'additional_data': additionalData
            });
        },

        continueToPayPal: function () {
            if (additionalValidators.validate()) {
                this.selectPaymentMethod();
                setPaymentMethodAction(this.messageContainer).done(
                    function () {
                        customerData.invalidate(['cart']);
                        $.mage.redirect(
                            window.checkoutConfig.payment.paypalExpress.redirectUrl[quote.paymentMethod().method]
                        );
                    }
                );

                return false;
            }
        }
    });
});
