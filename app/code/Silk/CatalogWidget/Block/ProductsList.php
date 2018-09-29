<?php
namespace Silk\CatalogWidget\Block;

use Magento\Framework\Serialize\Serializer\Json;

class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
{
    public $urlHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = [],
        Json $json = null
    ) {
        parent::__construct($context, $productCollectionFactory, $catalogProductVisibility,  $httpContext, $sqlBuilder, $rule, $conditionsHelper, $data);
        $this->urlHelper = $urlHelper;
    }

    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\CatalogWidget\Block\Product\ProductsList'));
        return parent::_toHtml();
    }

    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
}
