import * as $ from 'jquery';
import lazyload from 'LazyLoad';
import popup from 'popup';
import flexslider from 'magestore/flexslider';
import owlCarousel from 'carousel';
import Masonry from 'Magefan_Blog/js/masonry.pkgd.min';
import bridget from 'bridget';
import mage from 'mage/mage';
import ajaxScroll from 'ajaxScroll';
import swipe from 'touchSwipe';
import storage from 'mage/storage';

$(() => {
    let $progressBar = null,
        $bar = null,
        $elem = null,
        isPause = null,
        tick = null,
        percentTime = null,
        time = 5,
        counter = 0,
        settings = {},
        modes = {
            iframe: 'iframe',
            popup: 'popup'
        },
        standards = {
            strict: 'strict',
            loose: 'loose',
            html5: 'html5'
        };

    const lazyParams = window.lazyLoad,
        validate = window.validate,
        ajaxscroll = window.ajaxscroll,
        pwa = window.pwa,
        flexSlider = window.flexSlider || [];
    
    let quickView = {
        displayContent: function(prodUrl) {
            if (!prodUrl.length) {
                return false;
            }

            let url = window.quickView.baseUrl + 'weltpixel_quickview/index/updatecart';
            let showMiniCart = parseInt(window.quickView.showMiniCart);
            window.quickView.showMiniCartFlag = false;

            $.magnificPopup.open({
                items: {
                    src: prodUrl
                },
                type: 'iframe',
                closeOnBgClick: false,
                preloader: true,
                tLoading: '',
                callbacks: {
                    open: function() {
                        $('.mfp-preloader').css('display', 'block');
                    },
                    beforeClose: function() {
                        $('[data-block="minicart"]').trigger('contentLoading');
                        $.ajax({
                            url: url,
                            method: "POST"
                        });
                    },
                    close: function() {
                        $('.mfp-preloader').hide();
                    },
                    afterClose: function() {
                        if (window.quickView.showMiniCartFlag && showMiniCart) {
                            $('html, body').animate({ scrollTop: 0 }, 'slow');
                            setTimeout(function(){
                                if (!$('.block-minicart').is(':visible')) {
                                    $('.action.showcart').trigger('click');
                                }
                            }, 1000);
                        }
                    }
                }
            });
        }
    };

    function disabledMouseWheel() {
        var w = window.innerWidth;

        if (w <= 640) {
            if (document.addEventListener) {
                document.addEventListener('DOMMouseScroll', scrollF, false);
            }

            window.onmousewheel = document.onmousewheel = scrollF;
        }
    }

    function scrollF(e) {
        e = e || window.event;
        if (e.preventDefault) {
            e.preventDefault();
            e.stopPropagation();
        } else {
            e.cancelBubble=true;
            e.returnValue = false;
        }

        return false;
    }

    disabledMouseWheel();

    $('.lazy').lazyload({
        offset: lazyParams.offset,
		offsetVertical: lazyParams.offsetVertical,
		offsetHorizontal: lazyParams.offsetHorizontal,
		offsetTop: lazyParams.offsetTop,
		offsetBottom: lazyParams.offsetBottom,
		offsetLeft: lazyParams.offsetLeft,
		offsetRight: lazyParams.offsetRight,
		throttle: lazyParams.throttle,
        effec: lazyParams.effect,
        debounce: lazyParams.debounce,
		unload: lazyParams.unload,
		delay: lazyParams.delay,
        callback: function (element, op) {
            let $parent = $(element).parent();
            $(element).removeClass('lazy').addClass('loaded');
            
            if ($parent.hasClass('poster')) {
                $(element).next().addClass('loaded');
            }
        }
    });

    $('.lazy-bg').lazyload({
        offset: lazyParams.offset,
		offsetVertical: lazyParams.offsetVertical,
		offsetHorizontal: lazyParams.offsetHorizontal,
		offsetTop: lazyParams.offsetTop,
		offsetBottom: lazyParams.offsetBottom,
		offsetLeft: lazyParams.offsetLeft,
		offsetRight: lazyParams.offsetRight,
		throttle: lazyParams.throttle,
        effec: lazyParams.effect,
        debounce: lazyParams.debounce,
		unload: lazyParams.unload,
		delay: 0,
        callback: function (element, op) {
            $(element).removeClass('lazy-bg').addClass('loaded');
        }
    });

    bridget('masonry', Masonry, $);
    $('.waterfall').masonry({
        itemSelector: '.grid',
        gutter: 30,
        percentPosition: true
    });

    $('.owl-blog').owlCarousel({
        items: 4,
        lazyLoad: true,
        loop: true,
        nav: true,
        margin: 30,
        dots: false,
        autoplay: true,
        autoplayTimeout: 7000,
        navText: [
            '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-left"></use></svg>',
            '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-right"></use></svg>'
        ],
        responsive: {
            0: {
                items: 1,
                nav: false,
                dots: false
            },
            640: {
                items: 2,
                nav: false,
                dots: false
            },
            768: {
                items: 2,
                dots: true
            },
            1200: {
                items: 3,
                dots: true
            },
            1440: {
                items: 4,
                dots: false
            }
        }
    });

    $('.owl-comparison').owlCarousel({
        items: 4,
        lazyLoad: true,
        loop: false,
        nav: true,
        dots: false,
        autoplay: false,
        navText: [
            '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-left-square"></use></svg>',
            '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-right-square"></use></svg>'
        ],
        responsive: {
            0: {
                items: 1
            },
            460: {
                items: 2
            },
            900: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    });

    $('.owl-review').owlCarousel({
        items: 3,
        lazyLoad: true,
        loop: false,
        nav: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 4000,
        navText: [
            '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-left-square"></use></svg>',
            '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-right-square"></use></svg>'
        ],
        responsive: {
            0: {
                items: 1 
            },
            460: {
                items: 2
            },
            900: {
                items: 3
            },
            1200: {
                items: 3
            }
        }
    });

    $('.owl-theme').owlCarousel({
        items: 4,
        lazyLoad: true,
        loop: false,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 5000,
        navText: [
            '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-left-square"></use></svg>',
            '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-right-square"></use></svg>'
        ],
        responsive: {
            0: {
                items: 1
            },
            460: {
                items: 2
            },
            900: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    });

    $('.print').on('click', (e) => {
        $('.checkout-detail').printArea({
            mode: 'iframe',
            popClose: true
        });
    });

    $('.stamp').on('click', (e) => {
        $('.table-wrapper').printArea({
            mode: 'iframe',
            popClose: true
        });
    });

    $('.show-detail').on('click', (e) => {
        let _this = $(e.currentTarget);
        if (_this.hasClass('active')) {
            _this.removeClass('active');
            $('.checkout-body').slideDown();
        } else {
            _this.addClass('active');
            $('.checkout-body').slideUp();
        }
    });

    $('body').on('click', '.weltpixel-quickview', (e) => {
        let prodUrl = $(e.currentTarget).attr('data-quickview-url');

        if (prodUrl.length) {
            quickView.displayContent(prodUrl);
        }
    });

    if (typeof validate !== 'undefined') {
        if (validate.enabled) {
            validate.ignore = ':hidden:not(' + validate.ignore.ignore + ')';
        } else {
            validate.ignore = validate.ignore ? ':hidden:not(' + validate.ignore + ')' : ':hidden';
        }

        $('.create-individual').mage('validation', {
            ignore: validate.ignore
        }).find('input:text').attr('autocomplete', 'off');

        $('.create-enterprise').mage('validation', {
            ignore: validate.ignore
        }).find('input:text').attr('autocomplete', 'off');
    }

    $('.tab-link').on('click', (e) => {
        let activeDom = $(e.currentTarget);
        let toggle = activeDom.data('toggle');

        if (!activeDom.hasClass('active')) {
            activeDom.addClass('active').siblings().removeClass('active');
            $('.' + toggle).addClass('active').siblings().removeClass('active');
            $('.create-' + toggle).validation('clearError');
        }
    });
    
    $('.close-banner').on('click', (e) => {
        $('.banner').slideUp('slow');
        $('.page-wrapper').removeClass('fixed');
    });

    $('.cos.item').swipe({
        swipeLeft: function(event, direction, distance) {
            $(event.currentTarget).addClass('swipe');
        },
        swipeRight: function(event, direction, distance) {
            $(event.currentTarget).removeClass('swipe');
        },
        swipeUp: function (event, direction, distance) {
            let $scroll = document.documentElement.scrollTop || document.body.scrollTop;
            $('html, body').animate({
                scrollTop: $scroll + distance * 2
            }, 500);
        },
        swipeDown: function (event, direction, distance) {
            let $scroll = document.documentElement.scrollTop || document.body.scrollTop;
            $('html, body').animate({
                scrollTop: $scroll - distance * 2
            }, 500);
        },
        threshold: 0,
        fingers: 'all'
    });

    if (flexSlider.length > 0) {
        $.each(flexSlider, (key, item) => {
            let htmlId = item.htmlId;
            let flex_one = item.flex_one;
            let flex_two = item.flex_two;
            let flex_three = item.flex_three;
            switch (item.style) {
                case flex_one:
                    $('.' + htmlId).flexslider({
                        animation: item.animation,
                        slideshowSpeed: item.speed,
                        prevText: '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-left"></use></svg>',
                        nextText: '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-right"></use></svg>'
                    });
                    break;
                case flex_two:
                    $('.' + htmlId).flexslider({
                        animation: item.animation,
                        controlNav: 'thumbnails',
                        slideshowSpeed: item.speed,
                        prevText: '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-left"></use></svg>',
                        nextText: '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-right"></use></svg>'
                    });
                    break;
                case flex_three:
                    $('#' + htmlId + '-carousel').flexslider({
                        animation: 'slide',
                        controlNav: false,
                        animationLoop: true,
                        slideshow: true,
                        itemWidth: 210,
                        itemMargin: 5,
                        asNavFor: '#' + htmlId
                    });
                    $('#' + htmlId).flexslider({
                        animation: item.animation,
                        controlNav: false,
                        animationLoop: true,
                        slideshow: true,
                        sync: '#' + htmlId + '-carousel',
                        slideshowSpeed: item.speed
                    });
                    break;
                default:
                    $('.' + htmlId).flexslider({
                        animation: 'slide',
                        animationLoop: true,
                        itemWidth: 400,
                        slideshow: true,
                        itemMargin: 5,
                        minItems: 2,
                        maxItems: 4
                    });
                    break;
            }
        });
    }

    $('.blog-flexslider').flexslider({
        animation: 'slide',
        itemWidth: 460,
        itemMargin: 65,
        slideshowSpeed: 5000,
        move: 1,
        slideshow: false,
        animationLoop: true,
        mousewheel: true,
        controlNav: false,
        directionNav: false
    });

    let scrollFunc = () => {
        let $w = window.innerWidth;
        let $scroll = document.documentElement.scrollTop || document.body.scrollTop;
        let $sliderH = Math.abs($('.magestore-bannerslider').height() - $('.inner-wrapper').height());
        let $quickH = $('.page-main').offset().top + $('.page-main').height();

        if ($scroll >= $sliderH) {
            $('.page-header').addClass('active');
            $('.website').fadeIn('slow');
        } else {
            $('.page-header').removeClass('active');
            $('.website').fadeOut('slow').removeClass('open');
        }

        if ($scroll >= $quickH && $w > 767) {
            $('.quick-add').addClass('active');
            $('.action.pop').addClass('show');
        } else {
            $('.quick-add').removeClass('active');
            $('.action.pop').removeClass('show');
        }
    };

    scrollFunc();

    $(window).on('scroll resize', () => {
        scrollFunc();
    });

    $('.cart-add').on('click', (e) => {
        let cart = $(e.currentTarget).next();
        let qty = parseInt(cart.val()) + 1;
        let data = {
            'item_id': cart.attr('id'),
            'item_qty': qty
        };

        if ($(e.currentTarget).hasClass('cart')) {
            updateCart(data, cart, qty);
        } else {
            cart.val(qty);
        }
    });

    $('.cart-minus').on('click', (e) => {
        let cart = $(e.currentTarget).prev();
        let qty = parseInt(cart.val()) - 1;
        let data = {
            'item_id': cart.attr('id'),
            'item_qty': qty
        };

        if ($(e.currentTarget).hasClass('cart')) {
            updateCart(data, cart, qty);
        } else {
            if (qty > 0) {
                cart.val(qty);
            }
        }
    });

    const updateCart = ((data, element, qty) => {
        data = $.extend(data, {
            'form_key': $.mage.cookies.get('form_key')
        });

        storage.post(
            'checkout/sidebar/updateItemQty/',
            data,
            true,
            'application/x-www-form-urlencoded'
        ).done(function (response) {
            if (response.success) {
                if (qty > 0) {
                    element.val(qty);
                }
            }
        }).fail(function (error) {
            console.error(JSON.stringify(error));
        });
    });

    document.addEventListener('touchmove', function(event) {
        $('.website').removeClass('open');
    }, false); 

    $.fn.printArea = function (options) {
        let idPrefix = 'printArea_';
        $.extend(settings, {
            mode: modes.iframe,
            standard: standards.html5,
            popHt: 500,
            popWd: 400,
            popX: 200,
            popY: 200,
            popTitle: '',
            popClose: false,
            extraCss: '',
            extraHead: '',
            retainAttr: ["id", "class", "style"]
        }, options);

        counter++;
        $("[id^=" + idPrefix + "]").remove();

        settings.id = idPrefix + counter;

        var $printSource = $(this);
        var printWindow = PrintArea.getPrintWindow();

        PrintArea.write(printWindow.doc, $printSource);
        setTimeout(function () {
            PrintArea.print(printWindow);
        }, 1000);
    };

    let PrintArea = {
        print: function (pWin) {
            let window = pWin.win;

            $(pWin.doc).ready(function () {
                window.focus();
                window.print();

                if (settings.mode == modes.popup && settings.popClose) {
                    setTimeout(function () {
                        window.close();
                    }, 2000);
                }
            });
        },
        write: function (pDoc, $ele) {
            pDoc.open();
            pDoc.write(PrintArea.docType() + "<html>" + PrintArea.getHead() + PrintArea.getBody($ele) + "</html>");
            pDoc.close();
        },
        docType: function () {
            if (settings.mode == modes.iframe) return "";

            if (settings.standard == standards.html5) return "<!DOCTYPE html>";

            let transitional = settings.standard == standards.loose ? " Transitional" : "";
            let dtd = settings.standard == standards.loose ? "loose" : "strict";

            return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01' + transitional + '//EN" "http://www.w3.org/TR/html4/' + dtd + '.dtd">';
        },
        getHead: function () {
            let extraHead = "";
            let links = "";

            if (settings.extraHead) settings.extraHead.replace(/([^,]+)/g, function (m) { extraHead += m });

            $(document).find("link")
                .filter(function () {
                    let relAttr = $(this).attr("rel");
                    return ($.type(relAttr) === 'undefined') == false && relAttr.toLowerCase() == 'stylesheet';
                })
                .filter(function () {
                    let mediaAttr = $(this).attr("media");
                    return $.type(mediaAttr) === 'undefined' || mediaAttr == "" || mediaAttr.toLowerCase() == 'print' || mediaAttr.toLowerCase() == 'all'
                })
                .each(function () {
                    links += '<link type="text/css" rel="stylesheet" href="' + $(this).attr("href") + '" >';
                });
            if (settings.extraCss) settings.extraCss.replace(/([^,\s]+)/g, function (m) { links += '<link type="text/css" rel="stylesheet" href="' + m + '">' });

            return "<head><title>" + settings.popTitle + "</title>" + extraHead + links + "</head>";
        },
        getBody: function (elements) {
            let htm = "";
            let attrs = settings.retainAttr;
            elements.each(function () {
                let ele = PrintArea.getFormData($(this));
                let attributes = "";
                for (let x = 0; x < attrs.length; x++) {
                    let eleAttr = $(ele).attr(attrs[x]);
                    if (eleAttr) attributes += (attributes.length > 0 ? " " : "") + attrs[x] + "='" + eleAttr + "'";
                }

                htm += '<div ' + attributes + '>' + $(ele).html() + '</div>';
            });

            return "<body>" + htm + "</body>";
        },
        getFormData: function (ele) {
            let copy = ele.clone();
            let copiedInputs = $("input,select,textarea", copy);
            $("input,select,textarea", ele).each(function (i) {
                let typeInput = $(this).attr("type");
                if ($.type(typeInput) === 'undefined') typeInput = $(this).is("select") ? "select" : $(this).is("textarea") ? "textarea" : "";
                let copiedInput = copiedInputs.eq(i);

                if (typeInput == "radio" || typeInput == "checkbox") copiedInput.attr("checked", $(this).is(":checked"));
                else if (typeInput == "text" || typeInput == "") copiedInput.attr("value", $(this).val());
                else if (typeInput == "select")
                    $(this).find("option").each(function (i) {
                        if ($(this).is(":selected")) $("option", copiedInput).eq(i).attr("selected", true);
                    });
                else if (typeInput == "textarea") copiedInput.text($(this).val());
            });
            return copy;
        },
        getPrintWindow: function () {
            switch (settings.mode) {
                case modes.iframe:
                    let f = new PrintArea.iframe();
                    return { win: f.contentWindow || f, doc: f.doc };
                case modes.popup:
                    let p = new PrintArea.popup();
                    return { win: p, doc: p.doc };
            }
        },
        iframe: function () {
            let frameId = settings.id;
            let iframeStyle = 'border:0;position:absolute;width:0px;height:0px;right:0px;top:0px;';
            let iframe;

            try {
                iframe = document.createElement('iframe');
                document.body.appendChild(iframe);
                $(iframe).attr({ style: iframeStyle, id: frameId, src: "#" + new Date().getTime() });
                iframe.doc = null;
                iframe.doc = iframe.contentDocument ? iframe.contentDocument : (iframe.contentWindow ? iframe.contentWindow.document : iframe.document);
            } catch (e) {
                throw e + '. iframes may not be supported in this browser.';
            }

            if (iframe.doc == null) throw "Cannot find document.";

            return iframe;
        },
        popup: function () {
            let windowAttr = "location=yes,statusbar=no,directories=no,menubar=no,titlebar=no,toolbar=no,dependent=no";
            windowAttr += ",width=" + settings.popWd + ",height=" + settings.popHt;
            windowAttr += ",resizable=yes,screenX=" + settings.popX + ",screenY=" + settings.popY + ",personalbar=no,scrollbars=yes";

            let newWin = window.open("", "_blank", windowAttr);
            newWin.doc = newWin.document;

            return newWin;
        }
    };

    let SgyIAS = {
        debug: false,
        _log: function (object) {},
        init: function () {
            let config = {
                item: ajaxscroll.item,
                container: ajaxscroll.container,
                next: ajaxscroll.next,
                pagination: ajaxscroll.pagination,
                parameter: ajaxscroll.parameter,
                delay: 600,
                negativeMargin: ajaxscroll.negativeMargin,
                history: {
                    prev: '.prev'
                },
                noneleft: {
                    text: ajaxscroll.noneText,
                    html: '<div class="ias-noneleft">{text}</div>'
                },
                spinner: {
                    src: ajaxscroll.loaderSrc,
                    html: '<div class="ias-spinner"><img src="{src}"/>' + ajaxscroll.loaderHtml + '</div>'
                },
                trigger: {
                    text: ajaxscroll.triggerText,
                    html: '<div class="ias-trigger ias-trigger-next"><a>{text}</a></div>',
                    textPrev: ajaxscroll.textPrev,
                    htmlPrev: '<div class="ias-trigger ias-trigger-prev"><a>{text}</a></div>',
                    offset: ajaxscroll.offset
                }
            };

            if (window.ias_config) {
                $.extend(config, window.ias_config);
            }

            SgyIAS._log({ extension: 'ias', config: config });
            window.ias = $.ias(config);

            SgyIAS._log({ extension: 'paging' });
            window.ias.extension(new IASPagingExtension());

            SgyIAS._log({ extension: 'spinner' });
            window.ias.extension(new IASSpinnerExtension(config.spinner));

            SgyIAS._log({ extension: 'noneleft' });
            window.ias.extension(new IASNoneLeftExtension(config.noneleft));

            SgyIAS._log({ extension: 'trigger' });
            window.ias.extension(new IASTriggerExtension(config.trigger));

            window.ias.on('scroll', function (scrollOffset, scrollThreshold) {
                SgyIAS._log({ eventName: 'scroll', scrollOffset: scrollOffset, scrollThreshold: scrollThreshold });
            });

            window.ias.on('load', function (event) {
                SgyIAS._log({ eventName: 'load', event: event });
            });

            window.ias.on('loaded', function (data, items) {
                SgyIAS._log({ eventName: 'loaded', data: data, items: items });
            });

            window.ias.on('render', function (items) {
                SgyIAS._log({ eventName: 'render', items: items });
                $(items).find('.lazy').lazyload({
                    offset: lazyParams.offset,
                    offsetVertical: lazyParams.offsetVertical,
                    offsetHorizontal: lazyParams.offsetHorizontal,
                    offsetTop: lazyParams.offsetTop,
                    offsetBottom: lazyParams.offsetBottom,
                    offsetLeft: lazyParams.offsetLeft,
                    offsetRight: lazyParams.offsetRight,
                    throttle: lazyParams.throttle,
                    effec: lazyParams.effect,
                    debounce: lazyParams.debounce,
                    unload: lazyParams.unload,
                    delay: lazyParams.delay,
                    callback: function (element, op) {
                        $(element).removeClass('lazy').addClass('loaded');
                    }
                });
            });

            window.ias.on('noneLeft', function () {
                SgyIAS._log({ eventName: 'noneLeft' });
            });

            window.ias.on('next', function (url) {
                SgyIAS._log({ eventName: 'next', url: url });
            });

            window.ias.on('ready', function () {
                SgyIAS._log({ eventName: 'ready' });
            });
        }
    };

    if (ajaxscroll !== undefined) {
        SgyIAS.init();
    }

    $.fn.simulateSelect = function (options) {
        let defaults = {
            event: 'click',
            target: '#sorter'
        };

        options = $.extend(defaults, options);
        let element = $(this);

        element.on(options.event, (e) => {
            let _this = $(e.currentTarget);
            if (_this.hasClass('on')) {
                _this.removeClass('on');
                element.find(options.container).slideUp('fast');
            } else {
                _this.addClass('on');
                element.find(options.container).slideDown('fast');
            }
        });

        element.on(options.event, options.container + '> li', (e) => {
            e.stopPropagation();
            let p = $(e.currentTarget).find('p');
            let _node = p.text();
            let _key = p.attr('key');
            element.find(options.link).text(_node);
            element.find(options.container).hide();
            $(options.target).find('option[value="' + _key + '"]').attr('selected', true);
            $(options.target).trigger('change');
        });
    };

    $.fly = function (element, options) {
        let defaults = {
            autoPlay: true,
            vertex_Rtop: 20,
            speed: 1.2,
            start: {},
            end: {},
            onEnd: $.noop
        };

        let self = this,
            $element = $(element);

        self.init = function (options) {
            this.setOptions(options);
            !!this.settings.autoPlay && this.play();
        };

        self.setOptions = function (options) {
            this.settings = $.extend(true, {}, defaults, options);
            let settings = this.settings,
                start = settings.start,
                end = settings.end;

            $element.css({ marginTop: '0px', marginLeft: '0px', position: 'fixed' }).appendTo('body');
            if (end.width != null && end.height != null) {
                $.extend(true, start, {
                    width: $element.width(),
                    height: $element.height()
                });
            }

            let vertex_top = Math.min(start.top, end.top) - Math.abs(start.left - end.left) / 3;
            if (vertex_top < settings.vertex_Rtop) {
                vertex_top = Math.min(settings.vertex_Rtop, Math.min(start.top, end.top));
            }

            let distance = Math.sqrt(Math.pow(start.top - end.top, 2) + Math.pow(start.left - end.left, 2)),
                steps = Math.ceil(Math.min(Math.max(Math.log(distance) / 0.05 - 75, 30), 100) / settings.speed),
                ratio = start.top == vertex_top ? 0 : -Math.sqrt((end.top - vertex_top) / (start.top - vertex_top)),
                vertex_left = (ratio * start.left - end.left) / (ratio - 1),
                curvature = end.left == vertex_left ? 0 : (end.top - vertex_top) / Math.pow(end.left - vertex_left, 2);

            $.extend(true, settings, {
                count: -1,
                steps: steps,
                vertex_left: vertex_left,
                vertex_top: vertex_top,
                curvature: curvature
            });
        };

        self.play = function () {
            this.move();
        };

        self.move = function () {
            let settings = this.settings,
                start = settings.start,
                count = settings.count,
                steps = settings.steps,
                end = settings.end;
            let left = start.left + (end.left - start.left) * count / steps,
                top = settings.curvature == 0 ? start.top + (end.top - start.top) * count / steps : settings.curvature * Math.pow(left - settings.vertex_left, 2) + settings.vertex_top;
            if (end.width != null && end.height != null) {
                let i = steps / 2,
                    width = end.width - (end.width - start.width) * Math.cos(count < i ? 0 : (count - i) / (steps - i) * Math.PI / 2),
                    height = end.height - (end.height - start.height) * Math.cos(count < i ? 0 : (count - i) / (steps - i) * Math.PI / 2);
                $element.css({ width: width + 'px', height: height + 'px', 'font-size': Math.min(width, height) + 'px' });
            }
            $element.css({
                left: left + 'px',
                top: top + 'px'
            });
            settings.count++;
            let time = window.requestAnimationFrame($.proxy(this.move, this));
            if (count == steps) {
                window.cancelAnimationFrame(time);
                settings.onEnd.apply(this);
            }
        };

        self.destroy = function () {
            $element.remove();
        };

        self.init(options);
    };

    $.fn.fly = function (options) {
        return this.each(function () {
            if (undefined == $(this).data('fly')) {
                $(this).data('fly', new $.fly(this, options));
            }
        });
    };

    $('.link-faq').on('click', (e) => {
        var $faq = $(e.currentTarget).parent();

        if ($faq.hasClass('open')) {
            $faq.find('.menu').slideUp('slow');
            $faq.removeClass('open');
        } else {
            $faq.find('.menu').slideDown('slow');
            $faq.addClass('open');
        }
    });

    $('.open-filter').on('click', (e) => {
        var $this = $(e.currentTarget);

        if ($this.parent().hasClass('slide')) {
            $this.parent().removeClass('slide');
        } else {
            $this.parent().addClass('slide');
        }
    });

    $('.sorter-filter').on('click', (e) => {
        var $sidebar = $('.sidebar');

        if ($sidebar.hasClass('slide')) {
            $sidebar.removeClass('slide');
        } else {
            $sidebar.addClass('slide');
        }
    });

    $('.close-filter').on('click', (e) => {
        $('.sidebar').removeClass('slide');
    });

    $('.myboard-link').on('click', (e) => {
        var $el = $(e.currentTarget).parent();

        if ($el.hasClass('board-collapse')) {
            $el.removeClass('board-collapse');
        } else {
            $el.addClass('board-collapse');
        }
    });

    $('.footer-links').on('click', '.item', (e) => {
        var $this = $(e.currentTarget);

        if ($this.hasClass('open')) {
            $this.removeClass('open');
        } else {
            $this.addClass('open');
        }
    });

    $('.drop').on('click', (e) => {
        var $this = $(e.currentTarget).parent().parent();

        if ($this.hasClass('on')) {
            $this.removeClass('on');
        } else {
            $this.addClass('on');
        }
    });

    $('.values').on('click', '.lb', (e) => {
        var $this = $(e.currentTarget).parent();

        if ($this.hasClass('on')) {
            $this.removeClass('on');
        } else {
            $this.addClass('on');
        }
    });

    $('.show-all').on('click', (e) => {
        $(e.currentTarget).parent().addClass('open');
    });

    $('.btn-quick').on('click', '.cart', (e) => {
        $('body').addClass('overflow');
    });

    $('.btn-quick').on('click', '.checkout', (e) => {
        let $form = $('.quick-add form');
        let $input = $('<input type="hidden" name="checkout" value="1" />');
        $input.appendTo($form);

        $('body').addClass('overflow');
    });

    $('.btn-group').on('click', '.cancel', (e) => {
        let $form = $(e.currentTarget).closest('form');
        $form.find('input[name=checkout]').remove();
        $('body').removeClass('overflow');
    });

    $('.action.pop').on('click', (e) => {
        let $quick = $('.quick-add');

        if ($quick.hasClass('close')) {
            $(e.currentTarget).removeClass('cl');
            $quick.slideDown().removeClass('close');
        } else {
            $(e.currentTarget).addClass('cl');
            $quick.slideUp().addClass('close');
        }
    });

    $('.sorter-simulate').simulateSelect({
        link: 'cite',
        container: '.sorter-layer'
    });

    $('.action.tocheckout').on('click', (e) => {
        let $form = $(e.currentTarget).closest('form');
        let $input = $('<input type="hidden" name="checkout" value="1" />');
        $input.appendTo($form);
        $form.addClass('checkout').submit();
    });

    $('.read-more').on('mouseenter', (e) => {
        $(e.currentTarget).next().addClass('show');
    }).on('mouseleave', (e) => {
        $(e.currentTarget).next().removeClass('show');
    });

    let bannerResize = () => {
        let w = window.innerWidth;
        let images = $('.banner-img');

        $.each(images, function(k, img) {
            let url = null;

            if (w <= 767) {
                url = $(img).data('mobile');
            } else if (w > 767 && w <= 1024) {
                url = $(img).data('pad');
            } else {
                url = $(img).data('pc');
            }

            $(img).attr('src', url);
        });
    };

    bannerResize();

    $(window).on('resize', () => {
        bannerResize();
    });

    if (typeof window.__lc !== 'undefined') {
        let lc = document.createElement('script');
        let s = document.getElementsByTagName('head')[0]; 
        lc.async = true;
        lc.src = window.__lc.url;
        s.parentNode.insertBefore(lc, s);
    }

    if (pwa.enabled == '1') {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register(pwa.baseUrl + 'serviceWorker.js').then(function (registration) {
                console.log('ServiceWorker registration successful with scope: ', registration.scope);
            }, function (err) {
                console.log('ServiceWorker registration failed: ', err);
            });
        }
    
        function subscriberTopic() {
            firebase.messaging().getToken()
                .then(function (currentToken) {
                    if (currentToken) {
                        var subUrl = 'https://iid.googleapis.com/iid/v1/' + currentToken;
                        $.ajax({
                            url: pwa.ajaxUrl,
                            type: 'POST',
                            dataType: 'json',
                            data: {subUrl: subUrl},
                            success: function (response) {
                                console.log(response);
                            },
                            error: function () {
                                console.log("Cannot get data");
                            }
                        });
                    } else {
                        console.log('No Instance ID token available. Request permission to generate one.');
                    }
                })
                .catch(function (err) {
                    console.log('An error occurred while retrieving token. ', err);
                });
        }
    
        navigator.serviceWorker && navigator.serviceWorker.ready.then(function (serviceWorkerRegistration) {
            window.addEventListener('beforeinstallprompt', function (e) {
                e.userChoice.then(function (choiceResult) {
                    console.log(choiceResult.outcome);
    
                    if (choiceResult.outcome === 'dismissed') {
                        console.log('User cancelled home screen install');
                    }
                    else {
                        console.log('User added to home screen');
                    }
                });
            });
    
            firebase.messaging().useServiceWorker(serviceWorkerRegistration);
            firebase.messaging().requestPermission()
                .then(function () {
                    console.log('Notification permission granted.');
                    subscriberTopic();
                })
                .catch(function (err) {
                    console.log('Unable to get permission to notify.', err);
                });
    
            firebase.messaging().onMessage(function (payload) {
                console.log("Message received. ");
                if (payload.notification) {
                    let notificationTitle = payload.notification.title;
                    let notificationOptions = {
                        body: payload.notification.body,
                        icon: payload.notification.icon,
                        badge: payload.notification.icon
                    };
                    serviceWorkerRegistration.showNotification(notificationTitle, notificationOptions);
                }
            });
        });
    }
});