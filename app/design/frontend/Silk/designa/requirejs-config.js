var config = {
    map: {
        '*': {
            'iconfont': 'js/iconfont',
            'carousel': 'js/owl.carousel.min',
            'bridget': 'js/jquery.bridget',
            'touchSwipe': 'js/jquery.touchSwipe'
        }
    },
    shim: {
        'bridget': {
            deps: ['jquery'],
            exports: 'bridget'
        },
        'carousel' : {
            deps: ['jquery'],
            exports: 'carousel'
        },
        'touchSwipe': {
            deps: ['jqeury'],
            exports: 'touchSwipe'
        }
    },
    deps: [
        'iconfont'
    ]
};
