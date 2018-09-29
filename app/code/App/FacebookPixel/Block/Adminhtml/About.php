<?php
namespace App\FacebookPixel\Block\Adminhtml;

use Magento\Framework\Data\Form\Element\AbstractElement;

class About extends \Magento\Backend\Block\AbstractBlock implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var \App\FacebookPixel\Helper\Data
     */
    public $helper;
    
    /**
     * Constructor
     *
     * @param \App\FacebookPixel\Helper\Data $helper
     */
    public function __construct(\App\FacebookPixel\Helper\Data $helper)
    {
        $this->helper = $helper;
    }
    
    /**
     * Retrieve element HTML markup.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element  = null;
        $version  = $this->helper->getExtensionVersion();
        $html     = <<<HTML
<p>
<strong>App Facebook Pixel Extension v$version</strong><br />
Adds Facebook Pixel with Dynamic Ads code on appropriate pages. Passes W3C 
validation. Easy to install and use.
</p>
HTML;
        return $html;
    }
}
