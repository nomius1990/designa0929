<?php
namespace Silk\Customer\Block\Order;
class History extends \Magento\Sales\Block\Order\History
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Sales::order/history.phtml';
    /**
     * @var CollectionFactoryInterface
     */
    private $orderCollectionFactory;
    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $_image;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Catalog\Helper\ImageFactory $imageFactory,
        \Magento\Catalog\Model\Product $product,
        array $data = []
    ) {
        $this->_product     = $product;
        $this->_image       = $imageFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context, $orderCollectionFactory, $customerSession, $orderConfig, $data);
    }
    public function getImage($productId)
    {
        $product   = $this->_product->load($productId);
        $imageUrl  = $this->_image->create()->init($product, 'product_small_image')->setImageFile($product->getFile())->getUrl();
        return $imageUrl;
    }
    /**
     * @return CollectionFactoryInterface
     *
     * @deprecated 100.1.1
     */
    private function getOrderCollectionFactory()
    {
        if ($this->orderCollectionFactory === null) {
            $this->orderCollectionFactory = ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        }
        return $this->orderCollectionFactory;
    }
}