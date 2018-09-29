<?php
namespace Silk\Swatches\Block\Listing;
use Magento\Catalog\Model\Product;
class Configurable extends \Magento\Swatches\Block\Product\Renderer\Listing\Configurable
{
    protected function addAdditionalMediaData(array $swatch, $optionId, array $attributeDataArray)
    {
        if (isset($attributeDataArray['use_product_image_for_swatch'])
            && $attributeDataArray['use_product_image_for_swatch']
        ) {
            $variationMedia = [];
            if (! empty($variationMedia)) {
                $swatch['type'] = Swatch::SWATCH_TYPE_VISUAL_IMAGE;
                $swatch = array_merge($swatch, $variationMedia);
            }
        }
        return $swatch;
    }
    
    protected function getRendererTemplate()
    {
        return $this->_template;
    }

    protected function _toHtml()
    {
        $output = '';
        if ($this->isProductHasSwatchAttribute()) {
            $output = parent::_toHtml();
        }

        return $output;
    }

    protected function getSwatchAttributesData()
    {
        $result = [];
        $swatchAttributeData = parent::getSwatchAttributesData();
        foreach ($swatchAttributeData as $attributeId => $item) {
            if (!empty($item['used_in_product_listing'])) {
                $result[$attributeId] = $item;
            }
        }
        return $result;
    }

    public function getJsonConfig()
    {
        $this->unsetData('allow_products');
        return parent::getJsonConfig();
    }

    protected function getOptionImages()
    {
        return [];
    }

    protected function _getAdditionalConfig()
    {
        $config = parent::_getAdditionalConfig();
        if (!empty($this->getRequest()->getQuery()->toArray())) {
            $config['preSelectedGallery'] = $this->getProductVariationWithMedia(
                $this->getProduct(),
                $this->getRequest()->getQuery()->toArray()
            );
        }

        return $config;
    }

    private function getProductVariationWithMedia(
        Product $configurableProduct,
        array $additionalAttributes = []
    ) {
        $configurableAttributes = $this->getLayeredAttributesIfExists($configurableProduct, $additionalAttributes);
        if (!$configurableAttributes) {
            return [];
        }

        $product = $this->swatchHelper->loadVariationByFallback($configurableProduct, $configurableAttributes);

        return $product ? $this->swatchHelper->getProductMediaGallery($product) : [];
    }

    private function getLayeredAttributesIfExists(Product $configurableProduct, array $additionalAttributes)
    {
        $configurableAttributes = $this->swatchHelper->getAttributesFromConfigurable($configurableProduct);

        $layeredAttributes = [];

        $configurableAttributes = array_map(function ($attribute) {
            return $attribute->getAttributeCode();
        }, $configurableAttributes);

        $commonAttributeCodes = array_intersect(
            $configurableAttributes,
            array_keys($additionalAttributes)
        );

        foreach ($commonAttributeCodes as $attributeCode) {
            $layeredAttributes[$attributeCode] = $additionalAttributes[$attributeCode];
        }

        return $layeredAttributes;
    }
}