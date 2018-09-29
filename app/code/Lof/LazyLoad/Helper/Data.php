<?php
namespace Lof\LazyLoad\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context              $context       
     * @param \Magento\Framework\ObjectManagerInterface          $objectManager 
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager  
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig   
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
        ) {
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->_storeManager  = $storeManager;
        $this->_scopeConfig   = $context->getScopeConfig();
        $this->_request       = $context->getRequest();
    }

    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $result = $this->_scopeConfig->getValue(
            'loflazyload/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function isEnable() {
        $request = $this->_request;
        return true;
    }

    public function getMediaUrl() {
        $storeMediaUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
        ->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $storeMediaUrl;
    }

}