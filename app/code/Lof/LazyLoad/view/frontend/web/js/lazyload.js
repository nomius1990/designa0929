define(['jquery'], function ($) {
    'use strict';

    $.fn.lazyload = function (options) {
        var elements = this;
        var offset, poll, delay, useDebounce, unload;
        var callback = function (){};

        var load = {
            init: function (opts) {
                opts = opts || {};
                var offsetAll = opts.offset || 0;
                var offsetVertical = opts.offsetVertical || offsetAll;
                var offsetHorizontal = opts.offsetHorizontal || offsetAll;
                var optionToInt = function (opt, fallback) {
                    return parseInt(opt || fallback, 10);
                };
                offset = {
                    t: optionToInt(opts.offsetTop, offsetVertical),
                    b: optionToInt(opts.offsetBottom, offsetVertical),
                    l: optionToInt(opts.offsetLeft, offsetHorizontal),
                    r: optionToInt(opts.offsetRight, offsetHorizontal)
                };
                delay = opts.delay || 0;
                useDebounce = opts.debounce !== false;
                unload = !!opts.unload;
                callback = opts.callback || callback;
                load.render();
                if (document.addEventListener) {
                    document.addEventListener('scroll', function () {
                        load.debounceOrThrottle();
                    }, false);
                } else {
                    document.attachEvent('onscroll', function () {
                        load.debounceOrThrottle();
                    });
                }
            },
            render: function () {
                var nodes = elements;
                var length = nodes.length;
                var src, elem;
                var view = {
                    l: 0 - offset.l,
                    t: 0 - offset.t,
                    b: (window.innerHeight || document.documentElement.clientHeight) + offset.b,
                    r: (window.innerWidth || document.documentElement.clientWidth) + offset.r
                };
                for (var i = 0; i < length; i++) {
                    elem = nodes[i];
                    if (load.inView(elem, view)) {
                        if (unload) {
                            elem.setAttribute('data-placeholder', elem.src);
                        }
        
                        if (elem.getAttribute('data-origin') !== null) {
                            elem.style.backgroundImage = 'url(' + elem.getAttribute('data-origin') + ')';
                        } 
                        
                        if (elem.getAttribute('data-src') !== null) {
                            elem.src = elem.getAttribute('data-src');
                        }
        
                        if (!unload) {
                            elem.removeAttribute('data-src');
                            elem.removeAttribute('data-origin');
                        }
        
                        callback(elem, 'load');
                    } else if (unload && !!(src = elem.getAttribute('data-placeholder'))) {
                        if (elem.getAttribute('data-origin') !== null) {
                            elem.style.backgroundImage = 'url(' + src + ')';
                        } else {
                            elem.src = src;
                        }
        
                        elem.removeAttribute('data-placeholder');
                        callback(elem, 'unload');
                    }
                }
                if (!length) {
                    load.detach();
                }
            },
            detach: function () {
                if (document.removeEventListener) {
                    window.removeEventListener('scroll', load.debounceOrThrottle());
                } else {
                    window.detachEvent('onscroll', load.debounceOrThrottle());
                }

                clearTimeout(poll);
            },
            isHidden: function (element) {
                return (element.offsetParent === null);
            },
            inView: function (element, view) {
                if (load.isHidden(element)) {
                    return false;
                }
        
                var box = element.getBoundingClientRect();
                return (box.right >= view.l && box.bottom >= view.t && box.left <= view.r && box.top <= view.b);
            },
            debounceOrThrottle: function () {
                if (!useDebounce && !!poll) {
                    return;
                }
                clearTimeout(poll);
                poll = setTimeout(function () {
                    load.render();
                    poll = null;
                }, delay);
            }
        };

        load.init(options);
    }

    return $.fn.lazyload;
});