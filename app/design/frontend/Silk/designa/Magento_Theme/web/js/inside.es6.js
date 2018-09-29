import * as $ from 'jquery';
import * as ko from 'ko';
import * as _ from 'underscore';
const Component = require('uiComponent');
const customerData = require('Magento_Customer/js/customer-data');

let app = Component.extend({
    defaults: {
        cookieMessages: [],
        messages: []
    },
    cart: {},
    displaySubtotal: ko.observable(true),
    initialize: function () {
        this._super();
        this.cart = customerData.get('cart');
        this.cartData = customerData.get('cart');
        this.wishlist = customerData.get('wishlist');
        this.compare = customerData.get('compare-products');
        this.cookieMessages = $.cookieStorage.get('mage-messages');
        this.messages = customerData.get('messages').extend({
            disposableCustomerData: 'messages'
        });

        if (!_.isEmpty(this.messages().messages)) {
            customerData.set('messages', {});
        }

        this.update(this.cartData());
        this.cartData.subscribe((updatedCart) => {
            this.update(updatedCart);
        });
    },
    update: function (updatedCart) {
        _.each(updatedCart, (value, key) => {
            if (!this.cart.hasOwnProperty(key)) {
                this.cart[key] = ko.observable();
            }
            this.cart[key](value);
        });
    },
    getCartParam: function (name) {
        if (!_.isUndefined(name)) {
            if (!this.cart.hasOwnProperty(name)) {
                this.cart[name] = ko.observable();
            }
        }

        return this.cart[name]();
    },
    scroll: function (data, event) {
        $('html, body').animate({
            scrollTop: 0
        }, 800);
    }
});

export default app;