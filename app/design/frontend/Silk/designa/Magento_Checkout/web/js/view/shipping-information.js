define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/sidebar'
], function ($, Component, quote, priceUtils, stepNavigator, sidebarModel) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/shipping-information'
        },

        isDisplayShippingPriceExclTax: window.checkoutConfig.isDisplayShippingPriceExclTax,

        /**
         * @return {Boolean}
         */
        isVisible: function () {
            return !quote.isVirtual() && stepNavigator.isProcessed('shipping');
        },

        /**
         * @return {String}
         */
        getShippingMethodTitle: function () {
            var shippingMethod = quote.shippingMethod();

            return shippingMethod ? shippingMethod['carrier_title'] + ' - ' + shippingMethod['method_title'] : '';
        },

        getShippingMethodPrice: function () {
            var shippingMethod = quote.shippingMethod();
            var price = 0;
            
            if (this.isDisplayShippingPriceExclTax) {
                price = shippingMethod['price_excl_tax'];
            } else {
                price = shippingMethod['price_incl_tax'];
            }

            return priceUtils.formatPrice(price, quote.getPriceFormat());
        },

        /**
         * Back step.
         */
        back: function () {
            sidebarModel.hide();
            stepNavigator.navigateTo('shipping');
        },

        /**
         * Back to shipping method.
         */
        backToShippingMethod: function () {
            sidebarModel.hide();
            stepNavigator.navigateTo('shipping', 'opc-shipping_method');
        }
    });
});
