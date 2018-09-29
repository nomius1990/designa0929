define([
    'jquery',
    'mage/template',
    'jquery/ui'
], function ($, mageTemplate) {
    'use strict';

    $.widget('mage.dataPost', {
        options: {
            formTemplate: '<form action="<%- data.action %>" method="post">' +
            '<% _.each(data.data, function(value, index) { %>' +
            '<input name="<%- index %>" value="<%- value %>">' +
            '<% }) %></form>',
            postTrigger: ['a[data-post]', 'button[data-post]', 'span[data-post]'],
            formKeyInputSelector: 'input[name="form_key"]'
        },
        _create: function () {
            this._bind();
        },
        _bind: function () {
            var events = {};

            $.each(this.options.postTrigger, function (index, value) {
                events['click ' + value] = '_postDataAction';
            });

            this._on(events);
        },
        _postDataAction: function (e) {
            var params = $(e.currentTarget).data('post');

            e.preventDefault();
            this.postData(params);
        },
        postData: function (params) {
            var formKey = $(this.options.formKeyInputSelector).val(),
                $form;

            if (formKey) {
                params.data['form_key'] = formKey;
            }

            $form = $(mageTemplate(this.options.formTemplate, {
                data: params
            }));

            if (params.data.confirmation) {
                // uiConfirm({
                //     content: params.data.confirmationMessage,
                //     actions: {
                //         /** @inheritdoc */
                //         confirm: function () {
                //             $form.appendTo('body').hide().submit();
                //         }
                //     }
                // });
            } else {
                $form.appendTo('body').hide().submit();
            }
        }
    });

    $(document).dataPost();

    return $.mage.dataPost;
});
