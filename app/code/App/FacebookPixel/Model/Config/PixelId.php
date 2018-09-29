<?php
namespace App\FacebookPixel\Model\Config;

use Magento\Framework\Exception\LocalizedException;

class PixelId extends \Magento\Framework\App\Config\Value
{
    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value     = $this->getValue();
        $validator = \Zend_Validate::is(
            $value,
            'Regex',
            ['pattern' => '/^[0-9]+$/']
        );
        
        if (!$validator) {
            $message = __('Please correct Facebook Pixel ID: "%1".', $value);
            throw new LocalizedException($message);
        }
        
        return $this;
    }
}
