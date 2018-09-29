<?php
/**
 * Adminhtml AdminNotification inbox grid
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Catalog\Block;

class Title extends \Magento\Framework\View\Element\Template
{   
    protected $registry;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }


    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    public function getTitle() 
    {
        $title = $this->getCurrentCategory()->getName();

        return $title;
    }

    public function getCategoryBanner() {
        $bannerUrl = $this->getCurrentCategory()->getImageBanner();

        return $bannerUrl;
    }
}