<?php
/**
 * Landofcoder
 * 
 */

namespace Lof\All\Model\ResourceModel\License;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\All\Model\License', 'Lof\All\Model\ResourceModel\License');
    }
}
