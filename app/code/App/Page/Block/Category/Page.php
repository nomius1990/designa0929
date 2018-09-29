<?php
namespace App\Page\Block\Category;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\View\Result\PageFactory;

class Page extends \Magento\Catalog\Controller\Category\View
{
    public $_catalogSession;
    public $_catalogDesign;
    public $resultPageFactory;
    public $resultForwardFactory;
    public $layerResolver;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Design $catalogDesign,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context,$catalogDesign,$catalogSession,$coreRegistry,$storeManager,$categoryUrlPathGenerator,$resultPageFactory,$resultForwardFactory,$layerResolver,$categoryRepository);
        $this->_catalogDesign = $catalogDesign;
        $this->_catalogSession = $catalogSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->layerResolver = $layerResolver;
    }
   
    public function execute()
    {
        if ($this->_request->getParam(\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED)) {
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl());
        }
        $category = $this->_initCategory();
        if ($category) {
            $this->layerResolver->create(Resolver::CATALOG_LAYER_CATEGORY);
            $settings = $this->_catalogDesign->getDesignSettings($category);

            if ($settings->getCustomDesign()) {
                $this->_catalogDesign->applyCustomDesign($settings->getCustomDesign());
            }

            $this->_catalogSession->setLastViewedCategoryId($category->getId());
            $page = $this->resultPageFactory->create();

            if ($settings->getPageLayout()) {
                $page->getConfig()->setPageLayout($settings->getPageLayout());
            }
            $hasChildren = $category->hasChildren();
            if ($category->getIsAnchor()) {
                $type = $hasChildren ? 'layered' : 'layered_without_children';
            } else {
                $type = $hasChildren ? 'default' : 'default_without_children';
            }
            if (!$hasChildren) {
                $parentType = strtok($type, '_');
                $page->addPageLayoutHandles(['type' => $parentType], null, false);
            }
            $page->addPageLayoutHandles(['type' => $type], null, false);
            $page->addPageLayoutHandles(['id' => $category->getId()]);
            $layoutUpdates = $settings->getLayoutUpdates();
            if ($layoutUpdates && is_array($layoutUpdates)) {
                foreach ($layoutUpdates as $layoutUpdate) {
                    $page->addUpdate($layoutUpdate);
                    $page->addPageLayoutHandles(['layout_update' => md5($layoutUpdate)], null, false);
                }
            }

            return $page;
        } elseif (!$this->getResponse()->isRedirect()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}