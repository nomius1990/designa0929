<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\Blog\Block\Sidebar;

use Magento\Store\Model\ScopeInterface;

/**
 * Blog sidebar categories block
 */
class Categories extends \Magento\Framework\View\Element\Template
{
    use Widget;

    /**
     * @var string
     */
    protected $_widgetKey = 'categories';

    /**
     * @var \Magefan\Blog\Model\ResourceModel\Category\Collection
     */
    protected $_categoryCollection;
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magefan\Blog\Model\ResourceModel\Category\Collection $categoryCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magefan\Blog\Model\ResourceModel\Category\Collection $categoryCollection,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_categoryCollection = $categoryCollection;
    }

    /**
     * Get grouped categories
     * @return \Magefan\Blog\Model\ResourceModel\Category\Collection
     */
    public function getGroupedChilds()
    {
        $k = 'grouped_childs';
        if (!$this->hasData($k)) {
            $array = $this->_categoryCollection
                ->addActiveFilter()
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->setOrder('position')
                ->getTreeOrderedArray();

            foreach ($array as $key => $item) {
                $maxDepth = $this->maxDepth();
                if ($maxDepth > 0 && $item->getLevel() >= $maxDepth) {
                    unset($array[$key]);
                }
            }

            $this->setData($k, $array);
        }

        return $this->getData($k);
    }

    /**
     * Retrieve true if need to show posts count
     * @return int
     */
    public function showPostsCount()
    {
        $key = 'show_posts_count';
        if (!$this->hasData($key)) {
            $this->setData($key, (bool)$this->_scopeConfig->getValue(
                'mfblog/sidebar/'.$this->_widgetKey.'/show_posts_count',
                ScopeInterface::SCOPE_STORE
            ));
        }
        return $this->getData($key);
    }


    /**
     * Retrieve block identities
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Block::CACHE_TAG . '_blog_categories_widget'  ];
    }


    /**
     * Retrieve categories maximum depth
     * @return int
     */
    public function maxDepth()
    {
        $maxDepth = $this->_scopeConfig->getValue(
            'mfblog/sidebar/'.$this->_widgetKey.'/max_depth',
            ScopeInterface::SCOPE_STORE
        );
        
        return (int)$maxDepth;
    }
}