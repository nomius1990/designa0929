define([
    'Magento_Tax/js/view/checkout/summary/grand-total'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            src: null,
            staticImagePath: window.staticImagePath
        },
        initObservable: function () {
            this._super()
                .observe('src');

            this.src(staticImagePath);

            return this;
        },
        isDisplayed: function () {
            return true;
        }
    });
});
