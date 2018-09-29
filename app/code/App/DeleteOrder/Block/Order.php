<?php

namespace App\DeleteOrder\Block;

class Order extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        
        parent::__construct($context,$data);
        $buttonList = $context->getButtonList();

        $message = __('Are you sure you want to delete this order?');
        $buttonList->add(
            'delete_order',
            [
                'label' => __('Delete Orders'),
                'class' => 'delete-orders',
                'onclick' => "confirmSetLocation('{$message}', '{$this->getDeleteOrderUrl()}')"
            ]
        );
    }
   
    /**
     * Get delete order URL
     * 
     * @return string
     */
   public function getDeleteOrderUrl(){
       $order = $this->getOrder();
       
       return $this->getUrl('deleteorder/order/process',array('selected'=>$order->getId()));
   }
   
   /**
    * Retrieve order model object
    *
    * @return \Magento\Sales\Model\Order
    */
   public function getOrder()
   {
       return $this->_coreRegistry->registry('sales_order');
   }
}