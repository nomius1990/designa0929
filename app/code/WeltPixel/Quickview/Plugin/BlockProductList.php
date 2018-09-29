<?php

namespace WeltPixel\Quickview\Plugin;

class BlockProductList
{
    const XML_PATH_QUICKVIEW_ENABLED = 'weltpixel_quickview/general/enable_product_listing';
    const XML_PATH_QUICKVIEW_BUTTONSTYLE = 'weltpixel_quickview/general/button_style';
    
    /**
     * @var \Magento\Framework\UrlInterface 
     */
    protected $urlInterface;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface 
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
        ) {
        $this->urlInterface = $urlInterface;
        $this->scopeConfig = $scopeConfig;
    }
}
