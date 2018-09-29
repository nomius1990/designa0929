<?php
namespace Silk\Customer\Block;

class Title extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    public function getTitle()
    {
        return $this->getData('title');
    }
}
