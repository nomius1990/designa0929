<?php
namespace Lof\LazyLoad\Block;

class Script extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var \Lof\LazyLoad\Helper\Data
	 */
	protected $_helper;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context 
	 * @param \Lof\LazyLoad\Helper\Data                        $helper  
	 * @param array                                            $data    
	 */
    public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
    	\Lof\LazyLoad\Helper\Data $helper,
    	array $data = []
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
    }

    public function _toHtml() {
    	if (!$this->_helper->getConfig('general/enable')) {
    		return;
    	}
        if (!$this->_helper->isEnable()) {
            return;
        }
        return parent::_toHtml();
    }

}