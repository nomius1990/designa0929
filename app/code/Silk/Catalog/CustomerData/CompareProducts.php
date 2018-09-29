<?php
namespace Silk\Catalog\CustomerData;

class CompareProducts extends \Magento\Catalog\CustomerData\CompareProducts
{
    public $helper;
    public $productUrl;
    public $imageHelperFactory;

    public function __construct(
        \Magento\Catalog\Helper\Product\Compare $helper,
        \Magento\Catalog\Model\Product\Url $productUrl,
        \Magento\Catalog\Helper\Output $outputHelper,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory
    ) {
        parent::__construct($helper, $productUrl, $outputHelper);
        $this->helper = $helper;
        $this->productUrl = $productUrl;
        $this->outputHelper = $outputHelper;
        $this->imageHelperFactory = $imageHelperFactory;
    }

    public function getItems()
    {
        $items = [];
        foreach ($this->helper->getItemCollection() as $item) {
            $items[] = [
                'id' => $item->getId(),
                'image' => $this->getImageData($item),
                'product_url' => $this->productUrl->getUrl($item),
                'name' => $this->outputHelper->productAttribute($item, $item->getName(), 'name'),
                'product_price' => $this->productUrl->getProductPrice($item),
                'remove_url' => $this->helper->getPostDataRemove($item),
            ];
        }
        return $items;
    }

    public function getImageData($product)
    {
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->imageHelperFactory->create()
            ->init($product, 'wishlist_sidebar_block');

        $template = $helper->getFrame()
            ? 'Magento_Catalog/product/image'
            : 'Magento_Catalog/product/image_with_borders';

        $imagesize = $helper->getResizedImageInfo();

        $width = $helper->getFrame()
            ? $helper->getWidth()
            : (!empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth());

        $height = $helper->getFrame()
            ? $helper->getHeight()
            : (!empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight());

        return [
            'template' => $template,
            'src' => $helper->getUrl(),
            'width' => $width,
            'height' => $height,
            'alt' => $helper->getLabel(),
        ];
    }
}
