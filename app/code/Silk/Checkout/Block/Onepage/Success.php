<?php
namespace Silk\Checkout\Block\Onepage;
use Magento\Customer\Model\Session;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;
    protected $order;
    protected $quote;
    protected $_countryFactory;
    protected $currency;
    protected $_product;
    protected $imageHelper;
    protected $_customerSession;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Model\Product $product,
        Session $customerSession,
        array $data = []
    ) {
        $this->_session  = $checkoutSession;
        $this->_customerSession  = $customerSession;
        $this->quote     = $quote;
        $this->currency  = $currency;
        $this->_product  = $product;
        $this->imageHelper      = $imageHelper;
        $this->_countryFactory = $countryFactory;
        $order = $this->_session->getLastRealOrder();
        $this->order = $order;
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
    }
    public function getOrderInfo()
    {
       return $this->order;
    }
    public function formateMoney($price)
    {
        return $this->currency->format($price);
    }
    public function checkIsVitual()
    {
        $id = $this->order->getQuoteId();
        $model  = $this->quote->load($id);
        return $model->getIsVirtual();
    }
    public function getCountryName($countryCode){
        $country = $this->_countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }
    public function getSymbol()
    {
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        $symbol = $this->currency->load($currencyCode)->getCurrencySymbol();
        return $symbol;
    }
    public function getItemImage($productId)
    {
        $model          = $this->_product->load($productId);
        $data['url']    = $this->imageHelper->init(
            $model,
            'product_thumbnail_image'
        )->getUrl();
        $data['color']  = $model->getAttributeText('color');
        return $data;
    }
    public function getCustomerInfo()
    {
        if (!empty($this->_customerSession->getCustomerId())) {
            return true;
        } else {
            return false;
        }
    }
}