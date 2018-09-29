<?php
namespace App\Page\Block\Product;

use Magento\Framework\View\Result\Page as ResultPage;

class Page extends \Magento\Catalog\Helper\Product\View
{
    public $messageGroups;
    public $_coreRegistry = null;
    public $_catalogProduct = null;
    public $_catalogDesign;
    public $_catalogSession;
    public $messageManager;
    public $categoryUrlPathGenerator;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\Design $catalogDesign,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator,
        array $messageGroups = []
    ) {
        $this->_catalogSession = $catalogSession;
        $this->_catalogDesign = $catalogDesign;
        $this->_catalogProduct = $catalogProduct;
        $this->_coreRegistry = $coreRegistry;
        $this->messageGroups = $messageGroups;
        $this->messageManager = $messageManager;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
        parent::__construct($context, $catalogSession, $catalogDesign, $catalogProduct, $coreRegistry, $messageManager, $categoryUrlPathGenerator, $messageGroups);
    }

    public function initProductLayout(ResultPage $resultPage, $product, $params = null)
    {
        $settings = $this->_catalogDesign->getDesignSettings($product);
        $pageConfig = $resultPage->getConfig();

        if ($settings->getCustomDesign()) {
            $this->_catalogDesign->applyCustomDesign($settings->getCustomDesign());
        }

        if ($settings->getPageLayout()) {
            $pageConfig->setPageLayout($settings->getPageLayout());
        }

        $urlSafeSku = rawurlencode($product->getSku());
        $resultPage->addPageLayoutHandles(['id' => $product->getId(), 'sku' => $urlSafeSku]);
        $resultPage->addPageLayoutHandles(['type' => $product->getTypeId()], null, false);

        $currentCategory = $this->_coreRegistry->registry('current_category');
        $controllerClass = $this->_request->getFullActionName();
        if ($controllerClass != 'catalog-product-view') {
            $pageConfig->addBodyClass('catalog-product-view');
        }

        return $this;
    }
}