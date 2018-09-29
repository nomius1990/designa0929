<?php
namespace Silk\Import\Block\Adminhtml;
use Magento\Backend\Block\Template;

class Import extends Template
{
    public function __construct(
        Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

}