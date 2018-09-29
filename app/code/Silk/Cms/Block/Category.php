<?php
/**
 * Adminhtml AdminNotification inbox grid
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Cms\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class Category extends \Magento\Framework\View\Element\Template
{   
    protected $_collectionFactory;

    protected $_categoryFactory;

    protected $_categoryRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collecionFactory,
        \Magento\Catalog\Model\Category $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_collectionFactory = $collecionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryRepository = $categoryRepository;
    }

    public function getCategory($categoryName)
    {
        $collection = $this->_collectionFactory->create()->addAttributeToFilter('name', $categoryName)->setPageSize(1);

        if ($collection->getSize()) {
            $categoryId = $collection->getFirstItem()->getId();
            $categories = $this->_categoryFactory->load($categoryId)->getChildrenCategories();
        } else {
            $categories = [];
        }

        return $categories;
    }

    public function getCategoryImage($categoryId) 
    {
        $categoryIdElement = explode('-', $categoryId);
        $category = $this->_categoryRepository->get(end($categoryIdElement));
        $imageUrl = $category->getImageUrl();
 
        return $imageUrl;
    }
}