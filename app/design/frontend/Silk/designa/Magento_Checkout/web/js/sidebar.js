define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'jquery/ui',
    'mage/collapsible',
    'mage/cookies'
], function ($, customerData) {
    'use strict';

    $.widget('mage.sidebar', {
        options: {
            isRecursive: true,
            minicart: {
                maxItemsVisible: 3,
                addOpt: '[data-role="cart-add"]',
                minusOpt: '[data-role="cart-minus"]',
                qtyOpt: '.item-qty'
            }
        },
        _create: function () {
            this._initContent();
        },
        update: function () {
            $(this.options.targetElement).trigger('contentUpdated');
        },
        _initContent: function () {
            var self = this,
                events = {};

            events['click ' + this.options.button.checkout] = $.proxy(function () {
                var cart = customerData.get('cart'),
                    customer = customerData.get('customer');

                if (!customer().firstname && cart().isGuestCheckoutAllowed === false) {
                    $.cookie('login_redirect', this.options.url.checkout);

                    if (this.options.url.isRedirectRequired) {
                        location.href = this.options.url.loginUrl;
                    }

                    return false;
                }
                location.href = this.options.url.checkout;
            }, this);

            events['click ' + this.options.button.remove] =  function (event) {
                event.stopPropagation();
                self._removeItem($(event.currentTarget));
            };

            events['change ' + this.options.minicart.qtyOpt] = function (event) {
                self._updateItemQty($(event.target));
            };

            events['click ' + this.options.minicart.addOpt] = function (event) {
                event.stopPropagation();
                self._updateItemQty($(event.currentTarget));
            };

            events['click ' + this.options.minicart.minusOpt] = function (event) {
                event.stopPropagation();
                self._updateItemQty($(event.currentTarget));
            };

            events['focusout ' + this.options.item.qty] = function (event) {
                self._validateQty($(event.currentTarget));
            };

            this._on(this.element, events);
        },
        _isValidQty: function (origin, changed) {
            return origin != changed &&
                changed.length > 0 &&
                changed - 0 == changed &&
                changed - 0 > 0;
        },
        _validateQty: function (elem) {
            var itemQty = elem.data('item-qty');

            if (!this._isValidQty(itemQty, elem.val())) {
                elem.val(itemQty);
            }
        },
        _updateItemQty: function (elem) {
            var itemId = elem.data('cart-item');
            var opt = elem.data('role');
            var $input = $('#cart-item-' + itemId + '-qty');

            if (opt == 'cart-add') {
                $input.val(parseInt($input.val()) + 1);
                this._ajax(this.options.url.update, {
                    'item_id': itemId,
                    'item_qty': $input.val()
                }, elem, this._updateItemQtyAfter);
            } else if (opt == 'cart-minus') {
                if (parseInt($input.val()) == 1) {
                    this._ajax(this.options.url.remove, {
                        'item_id': itemId
                    }, elem, this._removeItemAfter);
                } else {
                    $input.val(parseInt($input.val()) - 1);

                    this._ajax(this.options.url.update, {
                        'item_id': itemId,
                        'item_qty': $input.val()
                    }, elem,this._updateItemQtyAfter);
                }
            } else {
                this._ajax(this.options.url.update, {
                    'item_id': itemId,
                    'item_qty': $input.val()
                }, elem, this._updateItemQtyAfter);
            }
        },
        _updateItemQtyAfter: function (elem) {
            
        },
        _removeItem: function (elem) {
            var itemId = elem.data('cart-item');

            this._ajax(this.options.url.remove, {
                'item_id': itemId
            }, elem, this._removeItemAfter);
        },
        _removeItemAfter: function (elem) {
            var productData = null;
            $.each(customerData.get('cart')().items, function(key, item) {
                if (Number(elem.data('cart-item')) === Number(item['item_id'])) {
                    productData = item;
                }
            });

            $(document).trigger('ajax:removeFromCart', productData['product_sku']);
        },
        _ajax: function (url, data, elem, callback) {
            var self = this;
            $.extend(data, {
                'form_key': $.mage.cookies.get('form_key')
            });

            $.ajax({
                url: url,
                data: data,
                type: 'post',
                dataType: 'json',
                context: this,
                beforeSend: function () {
                    elem.attr('disabled', 'disabled');
                    if (elem.parent().hasClass('qty')) {
                        elem.parent().find('.item-qty').hide();
                        elem.parent().find('.loading').show();
                    }
                },
                complete: function () {
                    elem.attr('disabled', null);
                    if (elem.parent().hasClass('qty')) {
                        elem.parent().find('.loading').hide();
                        elem.parent().find('.item-qty').show();
                    }
                }
            })
                .done(function (response) {
                    var msg;

                    if (response.success) {
                        callback.call(this, elem, response);
                        if ($('body').hasClass('checkout-cart-index')) {
                            self._updateSummary();
                        }
                    } else {
                        msg = response['error_message'];

                        if (msg) {
                            window.alert(msg);
                        }
                    }
                })
                .fail(function (error) {
                    console.log(JSON.stringify(error));
                });
        },
        _updateSummary: function () {
            var quote = require('Magento_Checkout/js/model/quote'),
                totalsDefaultProvider = require('Magento_Checkout/js/model/cart/totals-processor/default'),
                totalsProcessors = [],
                address = quote.shippingAddress(),
                type = address.getType();

            if (quote.isVirtual() || window.checkoutConfig.activeCarriers) {
                totalsProcessors['default'] = totalsDefaultProvider;
                totalsProcessors[type] ?
                    totalsProcessors[type].estimateTotals(address) :
                    totalsProcessors['default'].estimateTotals(address);
            }
        }
    });

    return $.mage.sidebar;
});