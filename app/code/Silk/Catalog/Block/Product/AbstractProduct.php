<?php
namespace Silk\Catalog\Block\Product;

class AbstractProduct extends \Magento\Catalog\Block\Product\AbstractProduct
{
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context, 
        array $data = []
    ) {
        $this->imageBuilder = $context->getImageBuilder();
        parent::__construct($context, $data);
    }

    public function inventImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->invent();
    }
}
