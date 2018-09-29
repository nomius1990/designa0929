<?php
/**
 * Landofcoder
 * 
 */

namespace Lof\All\Model;

class License extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\All\Model\ResourceModel\License');
    }
}
