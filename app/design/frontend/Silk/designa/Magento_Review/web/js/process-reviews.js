define([
    'jquery'
], function ($) {
    'use strict';
    function processReviews(url, fromPages) {
        $.ajax({
            url: url,
            cache: true,
            dataType: 'html',
            showLoader: false,
            loaderContext: $('.product.data.items')
        }).done(function (data) {
            $('#product-review-container').html(data);
            $('[data-role="product-review"] .pages a').each(function (index, element) {
                $(element).click(function (event) { 
                    processReviews($(element).attr('href'), true);
                    event.preventDefault();
                });
            });
        }).complete(function () {
            if (fromPages == true) {
                $('html, body').animate({
                    scrollTop: $('#review').offset().top - 50
                }, 300);
            }
        });
    }

    return function (config) {
        var reviewTab = $(config.reviewsTabSelector),
            requiredReviewTabRole = 'tab';

        if (reviewTab.attr('role') === requiredReviewTabRole && reviewTab.hasClass('active')) {
            processReviews(config.productReviewUrl);
        } else {
            reviewTab.one('beforeOpen', function () {
                processReviews(config.productReviewUrl);
            });
        }

        $(function () {
            $('.product-info-main .reviews-actions a').click(function (event) {
                var acnchor;

                event.preventDefault();
                acnchor = $(this).attr('href').replace(/^.*?(#|$)/, '');
                $('.product.data.items [data-role="content"]').each(function (index) { //eslint-disable-line
                    if (this.id == 'review') {
                        $('.product.data.items').tabs('activate', index);
                        $('html, body').animate({
                            scrollTop: $('#' + acnchor).offset().top - 50
                        }, 300);
                    }
                });
            });
        });
    };
});
