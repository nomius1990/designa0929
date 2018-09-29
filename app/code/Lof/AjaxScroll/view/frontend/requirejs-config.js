var config = {
	map: {
		'*': { 
			'ajaxScroll': 'Lof_AjaxScroll/js/ajaxScroll'
		}
	},
	deps: [
        'ajaxScroll'
    ],
	shim: {
		'ajaxScroll': {
			deps: ['jquery'],
            exports: 'ajaxScroll'
		}
	}
};