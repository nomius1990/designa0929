define([
    'jquery',
    'mage/translate',
    'jquery/ui'
], function ($, $t) {
    'use strict';

    $.widget('mage.catalogAddToCart', {
        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]',
            productStatusSelector: '.stock.available',
            addToCartButtonSelector: '.action.tocart',
            checkoutButtonSelector: '.action.tocheckout',
            addToCartButtonDisabledClass: 'disabled',
            addToCartButtonTextWhileAdding: '',
            addToCartButtonTextAdded: '',
            addToCartButtonTextDefault: ''
        },
        _create: function () {
            if (this.options.bindSubmit) {
                this._bindSubmit();
            }
        },
        _bindSubmit: function () {
            var self = this;

            this.element.on('submit', function (e) {
                e.preventDefault();
                self.submitForm($(this));
            });
        },
        isLoaderEnabled: function () {
            return this.options.processStart && this.options.processStop;
        },
        submitForm: function (form) {
            var addToCartButton, self = this;

            if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                self.element.off('submit');
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);
                addToCartButton.prop('disabled', true);
                addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
                form.submit();
            } else {
                self.ajaxSubmit(form);
            }
        },
        ajaxSubmit: function (form) {
            var self = this;

            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableAddToCartButton(form);

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function (res) {
                    var eventData, parameters;
                    $(document).trigger('ajax:addToCart', form.data().productSku);

                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        eventData = {
                            'form': form,
                            'redirectParameters': []
                        };

                        $('body').trigger('catalogCategoryAddToCartRedirect', eventData);
                        if (!res.onestep && eventData.redirectParameters.length > 0) {
                            parameters = res.backUrl.split('#');
                            parameters.push(eventData.redirectParameters.join('&'));
                            res.backUrl = parameters.join('#');
                        }

                        window.location = res.backUrl;

                        return;
                    }

                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }

                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated'); 
                    }

                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }
                    self.besselCart(form);
                    self.enableAddToCartButton(form);
                }
            });
        },
        besselCart: function (form) {
            var endLeft = 0,
                endTop = 0,
                flyer = null,
                scroll = $(document).scrollTop(),
                offset = $('.minicart').offset();

            var minicart = $('.showcart').offset();
            if (form.data('img-url')) {
                flyer = $('<img class="flyer" src="' + form.data('img-url') + '">');
            } else {
                var url = $('.fotorama__active .fotorama__img').attr('src');
                flyer = $('<img class="flyer" src="' + url + '">');
            }
            
            if (scroll >= 108) {
                endLeft = offset.left + 10;
                endTop = offset.top - scroll;
            } else {
                endLeft = minicart.left + 10;
                endTop = minicart.top - scroll;
            }
            
            flyer.fly({
                start: {
                    left: form.offset().left,
                    top: form.offset().top - scroll
                },
                end: {
                    left: endLeft,
                    top: endTop,
                    width: 0,
                    height: 0
                },
                onEnd: function() {
                    var $msg = $('.minicart .msg'); 
                    $msg.addClass('show');
                    $('body').removeClass('overflow');
                    if (!$('body').hasClass('cms-index-index')) {
                        $('html, body').animate({
                            scrollTop: 0
                        }, 800);
                    }
                    setTimeout(function () {
                        $msg.removeClass('show');
                    }, 1500);
                }
            });
        },
        disableAddToCartButton: function (form) {
            var addToCartButtonTextWhileAdding = this.options.addToCartButtonTextWhileAdding || $t('Adding...'),
                addToCartButton = $(form).find(this.options.addToCartButtonSelector),
                checkoutButton = $(form).find(this.options.checkoutButtonSelector);

            if ($(form).hasClass('checkout')) {
                checkoutButton.addClass(this.options.addToCartButtonDisabledClass);
                checkoutButton.find('span').text($t('Buying...'));
                checkoutButton.attr('title', $t('Buying...'));
            } else {
                addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(addToCartButtonTextWhileAdding);
                addToCartButton.attr('title', addToCartButtonTextWhileAdding);
            }
        },
        enableAddToCartButton: function (form) {
            var addToCartButtonTextAdded = this.options.addToCartButtonTextAdded || $t('Added'),
                self = this,
                addToCartButton = $(form).find(this.options.addToCartButtonSelector),
                checkoutButton = $(form).find(this.options.checkoutButtonSelector);
            
            if ($(form).hasClass('checkout')) {
                checkoutButton.find('span').text($t('Buying'));
                checkoutButton.attr('title', $t('Buying'));
    
                setTimeout(function () {
                    checkoutButton.removeClass(self.options.addToCartButtonDisabledClass);
                    checkoutButton.find('span').text($t('Buy Now'));
                    checkoutButton.attr('title', $t('Buy Now'));
                }, 1000);
            } else {
                addToCartButton.find('span').text(addToCartButtonTextAdded);
                addToCartButton.attr('title', addToCartButtonTextAdded);
    
                setTimeout(function () {
                    var addToCartButtonTextDefault = self.options.addToCartButtonTextDefault || $t('Add to Cart');
    
                    addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                    addToCartButton.find('span').text(addToCartButtonTextDefault);
                    addToCartButton.attr('title', addToCartButtonTextDefault);
                }, 1000);
            }
        }
    });

    return $.mage.catalogAddToCart;
});