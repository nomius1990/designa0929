define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'carousel'
], function ($, ko, Component, customerData) {
    'use strict';

    ko.bindingHandlers.slider = {
        init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            $(element).owlCarousel({
                items: 1,
                lazyLoad: true,
                loop: false,
                nav: true,
                dots: false,
                navText: [
                    '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-left-square"></use></svg>',
                    '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-right-square"></use></svg>'
                ]
            }); 
        }
    };

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.wishlist = customerData.get('wishlist');
        }
    });
});
