<?php
/**
 * @copyright Copyright
 */

namespace App\ProgressiveWebApp\Model\System\Config\Source;

class DisplayType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toArray() as $value => $label) {
            $options[] = ["value" => $value, "label" => $label];
        }

        return $options;
    }

    /**
     * Get options in "key=>value" format.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            "browser" => __("Web Page"),
            "minimal-ui" => __("Minimal UI"),
            "standalone" => __("Standalone App"),
            "fullscreen" => __("Fullscreen App"),
        ];
    }
}
