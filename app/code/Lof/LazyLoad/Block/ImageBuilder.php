<?php
namespace Lof\LazyLoad\Block;

use \Magento\Catalog\Helper\ImageFactory as HelperFactory;
use \Magento\Catalog\Block\Product\ImageFactory as ImageFactory;
use Magento\Framework\App\ResourceConnection;

class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{
    public $product;
    public $imageId;
    public $attributes = [];
    public $_connection;

    public function __construct(
        HelperFactory $helperFactory,
        ImageFactory $imageFactory,
        \Lof\LazyLoad\Helper\Data $helper,
        ResourceConnection $connection
    ) {
        $this->helperFactory = $helperFactory;
        $this->imageFactory  = $imageFactory;
        $this->_helper       = $helper;
        $this->_connection   = $connection;
    }

    /**
     * Create image block
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function create()
    {
        $connection = $this->_connection->getConnection();
        $eav_table = 'eav_attribute';
        $table = 'catalog_product_entity_varchar';
        $img_select = $connection->select()
            ->from($eav_table,['attribute_id'])
            ->where('attribute_code = ?', 'product_hover_image');
        $img_value = $connection->fetchRow($img_select);
        $select = $connection->select()
            ->from($table,['value'])
            ->where('attribute_id = ?',$img_value['attribute_id'])
            ->where('entity_id = ?',$this->product->getId());
        $value = $connection->fetchRow($select);

        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create()
            ->init($this->product, $this->imageId);

        if ($this->_helper->getConfig('general/enable')) {
            $template = $helper->getFrame()
                ? 'Lof_LazyLoad::product/image.phtml'
                : 'Lof_LazyLoad::product/image_with_borders.phtml';
        } else {
            $template = $helper->getFrame()
                ? 'Magento_Catalog::product/image.phtml'
                : 'Magento_Catalog::product/image_with_borders.phtml';
        }

        $imagesize = $helper->getResizedImageInfo();

        $data = [
            'data' => [
                'template' => $template,
                'image_url' => $helper->getUrl(),
                'width' => $helper->getWidth(),
                'height' => $helper->getHeight(),
                'label' => $helper->getLabel(),
                'ratio' =>  $this->getRatio($helper),
                'info'  => $value['value'],
                'custom_attributes' => $this->getCustomAttributes(),
                'resized_image_width' => !empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth(),
                'resized_image_height' => !empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight(),
            ],
        ];

        return $this->imageFactory->create($data);
    }

    public function invent()
    {
        $connection = $this->_connection->getConnection();
        $eav_table = 'eav_attribute';
        $table = 'catalog_product_entity_varchar';
        $img_select = $connection->select()
            ->from($eav_table,['attribute_id'])
            ->where('attribute_code = ?', 'product_hover_image');
        $img_value = $connection->fetchRow($img_select);
        $select = $connection->select()
            ->from($table,['value'])
            ->where('attribute_id = ?',$img_value['attribute_id'])
            ->where('entity_id = ?',$this->product->getId());
        $value = $connection->fetchRow($select);
        
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create()
            ->init($this->product, $this->imageId);

        $template = 'Magento_Catalog::product/image_with_carousel.phtml';

        $imagesize = $helper->getResizedImageInfo();

        $data = [
            'data' => [
                'template' => $template,
                'image_url' => $helper->getUrl(),
                'width' => $helper->getWidth(),
                'height' => $helper->getHeight(),
                'label' => $helper->getLabel(),
                'ratio' =>  $this->getRatio($helper),
                'info'  => $value['value'],
                'custom_attributes' => $this->getCustomAttributes(),
                'resized_image_width' => !empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth(),
                'resized_image_height' => !empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight(),
            ],
        ];

        return $this->imageFactory->create($data);
    }
}