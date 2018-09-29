var config = {
    map: {
        '*': {
            'designa': 'Magento_Theme/js/designa',
            'navigation': 'Magento_Theme/js/navigation',
            'inside': 'Magento_Theme/js/inside'
        }
    },
    deps: [
        'designa',
        'inside'
    ],
    shim: {
        'navigation': {
            deps: ['jquery', 'jquery/ui'],
            exports: 'navigation'
        },
        'inside': {
            deps: ['jquery', 'ko'],
            exports: 'inside'
        }
    }
};
