import * as $ from 'jquery';
import ui from 'jquery/ui';

$(() => {
    $.widget('caesar.navigation', {
        options: {
            responsive: null,
            activeLink: 'li.level-top',
            searchLink: '.action.search',
            closeLink: '.link-close',
            inputContainer: '.link-input',
            minicart: '.action.showcart',
            closeCart: '.action.close',
            toggleNav: '.nav-toggle',
            navLink: '.link-toggle',
            topmenuLink: '.level1 > p',
        },
        _create: function () {
            this.element.on('mouseenter', this.options.activeLink, $.proxy((e) => {
                let $current = $(e.currentTarget);
                if ($current.hasClass('level-top') && !$current.hasClass('over')) {
                    $current.addClass('over');
                }
            }, this)).on('mouseleave', this.options.activeLink, $.proxy((e) => {
                let $current = $(e.currentTarget);
                if ($current.hasClass('level-top')) {
                    $current.removeClass('over');
                }
            }, this));

            this.element.on('mouseenter', this.options.topmenuLink, $.proxy((e) => {
                let $current = $(e.currentTarget);
                let $parent = $current.parent();
                let $url = $current.find('.color').data('src');
                let $img = $('.thumbnail img');
                if ($url != '') {
                    $img.attr('src', $url);
                }
                $parent.removeClass('first').siblings().removeClass('first');
                $parent.addClass('active').siblings().removeClass('active');
            }, this));

            this._bindEvent();
        },
        _bindEvent: function () {
            $(this.options.searchLink).on('click', (e) => {
                $('.auto-search').addClass('auto');
            });

            $(this.options.closeLink).on('click', (e) => {
                $('.auto-search').removeClass('auto');
            });

            $(this.options.toggleNav).on('click', (e) => {
                $('.nav-sections').addClass('show');
                $('.nav-layer').addClass('on');
            });

            $(this.options.inputContainer).on('focus', (e) => {
                $('.auto-search').addClass('focus');
            }).on('blur', (e) => {
                $('.auto-search').removeClass('focus');
            });

            $(this.options.minicart).on('click', (e) => {
                $('.minicart-wrapper').addClass('active');
                $('.mini-layer').addClass('on');
            });

            $('.mini-layer').on('click', (e) => {
                $('.minicart-wrapper').removeClass('active');
                $(e.currentTarget).removeClass('on');
            });

            $(this.options.navLink).on('click', (e) => {
                let $current = $(e.currentTarget);

                if ($current.hasClass('on')) {
                    $current.parent().next().hide();
                    $current.removeClass('on');
                } else {
                    $current.parent().next().show();
                    $current.addClass('on');
                }
            });

            $('.nav.close').on('click' , (e) => {
                $('.nav-sections').removeClass('show');
                $('.nav-layer').removeClass('on');
            });

            $('.nav-layer').on('click', (e) => {
                $('.nav-sections').removeClass('show');
                $(e.currentTarget).removeClass('on');
            });

            $('body').on('click', this.options.closeCart, (e) => {
                e.stopPropagation();
                $('.minicart-wrapper').removeClass('active');
                $('.mini-layer').removeClass('on');
            });

            $('.product-item-photo').on('mouseenter', (e) => {
                let $current = $(e.currentTarget).find('img');
                let $url = $current.data('hover');
                let $loader = $current.data('loader');

                if ($url) {
                    $current.attr('src', $loader);
                    setTimeout(() => {
                        $current.attr('src', $url);
                    }, 500);
                }
            }).on('mouseleave', (e) => {
                let $current = $(e.currentTarget).find('img');
                let $hover = $current.data('hover');
                let $loader = $current.data('loader');

                if ($hover) {
                    $current.attr('src', $loader);
                    setTimeout(() => {
                        $current.attr('src', $current.data('init'));
                    }, 500);
                }
            });

            $('.section-item-title').on('click', (e) => {
                let $current = $(e.currentTarget);
                $('.section-item-title').removeClass('active');
                $('.section-item-content').removeClass('active');
                $current.addClass('active');
                $current.next().addClass('active');
            });
        }
    });

    return $.caesar.navigation;
});