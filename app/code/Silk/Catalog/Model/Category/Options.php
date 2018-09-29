<?php
namespace Silk\Catalog\Model\Category;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            '0' =>['value'=>'', 'label' => 'Please select a waterfall position'],
            '1' =>['value'=>'top', 'label' => 'Top'],
            '2' =>['value'=>'middle', 'label' => 'Middle'],
            '3' =>['value'=>'bottom', 'label' => 'Bottom']
        ];
    }
}