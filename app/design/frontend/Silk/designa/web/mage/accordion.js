/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/tabs'
], function ($, tabs) {
    'use strict';

    $.widget('mage.accordion', tabs, {
        options: {
            active: false,
            multipleCollapsible: false,
            openOnFocus: false
        },
        _callCollapsible: function () {
            var self = this,
                disabled = false,
                active = false;
            
            $.each(this.collapsibles, function (i) {
                disabled = active = false;

                if ($.inArray(i, self.options.disabled) !== -1) {
                    disabled = true;
                }

                if (self.options.active) {
                    active = true;
                }
                self._instantiateCollapsible(this, i, active, disabled);
            });
        },
        _toggleActivate: function (action, index) {
            var self = this;

            if ($.isArray(index && this.options.multipleCollapsible)) {
                $.each(index, function () {
                    self.collapsibles.eq(this).collapsible(action);
                });
            } else if (index === undefined && this.options.multipleCollapsible) {
                this.collapsibles.collapsible(action);
            } else {
                this._super(action, index);
            }
        },
        _handleDeepLinking: function () {
            if (!this.options.multipleCollapsible) {
                this._super();
            }
        },
        _closeOthers: function () {
            if (!this.options.multipleCollapsible) {
                this._super();
            }
            $.each(this.collapsibles, function () {
                $(this).on('beforeOpen', function () {
                    var section = $(this);

                    section.addClass('allow').prevAll().addClass('allow');
                    section.nextAll().removeClass('allow');
                });
            });
        }
    });

    return $.mage.accordion;
});
