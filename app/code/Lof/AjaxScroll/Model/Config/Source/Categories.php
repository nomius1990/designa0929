<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 */

namespace Lof\AjaxScroll\Model\Config\Source;

class Categories implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\User\Model\UserFactory
     */
	protected $_categoryFactory;
    
    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
	public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
	){
        $this->_categoryFactory = $categoryFactory;
	}

    public function toOptionArray()
    {   
        $result = [];
        $items = $this->_categoryFactory->create()->getCollection()->addAttributeToSelect(
            'name'
        )->addAttributeToSort(
            'entity_id',
            'ASC'
        )->load()->getItems();
        foreach ($items as $item) {
            $result[] = [
                'label' => $item->getName(),
                'value' => $item->getEntityId()
                ];
        }  
        return $result;
    }
}