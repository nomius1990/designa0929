<?php
namespace Silk\Customer\Block;

use Magento\Framework\Exception\NoSuchEntityException;

class Account extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry;

    protected $currentCustomer;

    protected $_orderCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Helper\View $helperView,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->currentCustomer = $currentCustomer;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_helperView = $helperView;
        parent::__construct($context, $data);
    }

    public function getCustomer()
    {
        try {
            return $this->currentCustomer->getCustomer();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    public function getName()
    {
        return $this->_helperView->getCustomerName($this->getCustomer());
    }

    public function getShippingCount() 
    {
        $shippingCount = 0;
        $order = $this->_orderCollectionFactory->create()->addFieldToSelect(['entity_id']);
        $order->addFieldToFilter('customer_id',$this->getCustomer()->getId());
        $order->addFieldToFilter('status','shipped');

        $shippingCount = count($order);

        return $shippingCount;
    }

    public function getHistoryCount() 
    {
        $historyCount = 0;
        $order = $this->_orderCollectionFactory->create()->addFieldToSelect(['entity_id']);
        $order->addFieldToFilter('customer_id',$this->getCustomer()->getId());
        $order->addFieldToFilter('status',['in'=>'complete,canceled,closed']);

        $historyCount = count($order);

        return $historyCount;
    }
}
