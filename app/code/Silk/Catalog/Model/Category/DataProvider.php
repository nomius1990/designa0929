<?php
namespace Silk\Catalog\Model\Category;

class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{

    protected function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['content'][] = 'image_banner'; // custom image field

        return $fields;
    }
}