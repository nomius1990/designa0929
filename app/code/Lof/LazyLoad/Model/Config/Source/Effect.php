<?php
namespace Lof\LazyLoad\Model\Config\Source;

class Effect implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'show', 'label' => __('Show')], ['value' => 'fadeIn', 'label' => __('FadeIn')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['show' => __('Show'), 'fadeIn' => __('FadeIn')];
    }
}
