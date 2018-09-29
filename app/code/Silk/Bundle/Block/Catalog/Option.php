<?php
namespace Silk\Bundle\Block\Catalog;

class Option extends \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\Radio
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Bundle::catalog/product/view/type/bundle/option/radio.phtml';
    
    public function getSelectionQtyTitlePrice($selection, $includeContainer = true)
    {
        $this->setFormatProduct($selection);
        $priceTitle = '<div class="bundle-info"><span class="product-name">' . $selection->getSelectionQty() * 1 . ' x ' . $this->escapeHtml($selection->getName()) . '</span>';

        $priceTitle .= $this->renderPriceString($selection, $includeContainer) . '</div>';

        return $priceTitle;
    }

    public function getSelectionTitlePrice($selection, $includeContainer = true)
    {
        $priceTitle = '<div class="bundle-info"><span class="product-name">' . $this->escapeHtml($selection->getName()) . '</span>';
        $priceTitle .= $this->renderPriceString($selection, $includeContainer) . '</div>';
        return $priceTitle;
    }
}
