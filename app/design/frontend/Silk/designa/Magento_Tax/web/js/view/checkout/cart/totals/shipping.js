define([
    'Magento_Tax/js/view/checkout/summary/shipping',
    'Magento_Checkout/js/model/quote',
    'mage/translate'
], function (Component, quote, $t) {
    'use strict';

    return Component.extend({
        /**
         * @override
         */
        isCalculated: function () {
            return !!quote.shippingMethod();
        },

        /**
         * @override
         */
        getShippingMethodTitle: function () {
            return '(' + $t('By Address') + ')';
        }
    });
});
