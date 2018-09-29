<?php
/**
 * @copyright Copyright
 */

namespace App\ProgressiveWebApp\Block\Base\Js;

use Magento\Framework\View\Element\Template;

class Main extends \Magento\Framework\View\Element\Template
{
    protected $_helper;

    public function __construct(
        Template\Context $context,
        \App\ProgressiveWebApp\Helper\Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getFirebaseScript()
    {
        return $this->_helper->getFirebaseScript();
    }

    public function getThemeColor()
    {
        return $this->_helper->getManifestThemeColor();
    }

    public function getFcmVersion()
    {
        return $this->_helper->getFcmVersion();
    }

    public function isEnabled()
    {
        return $this->_helper->isEnabled();
    }
}
