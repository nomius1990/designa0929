<?php
/**
 * Adminhtml AdminNotification inbox grid
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Theme\Block;

use Magento\Catalog\Model\CategoryFactory;

class Category extends \Magento\Framework\View\Element\Template
{
    protected $_config;
    protected $_categoryFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_config = $scopeConfig;
        $this->_categoryFactory = $categoryFactory;
    }

    public function getNavCategory()
    {
        $category = $this->_categoryFactory->create();
        $id = $this->_config->getValue('catalog/footer_category/category_id');
        $category->load($id);
        $array = $category->getChildrenCategories();

        return $array;
    }
}