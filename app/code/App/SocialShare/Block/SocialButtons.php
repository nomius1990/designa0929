<?php

namespace App\SocialShare\Block;

class SocialButtons extends \Magento\Framework\View\Element\Template {

	protected $_template = 'buttons.phtml';

	protected $_scopeConfig;
	protected $_registry;
	private $product;
	/**
	 * [__construct description]
	 * @param \Magento\Framework\View\Element\Template\Context                $context                 [description]
	 * @param array                                                           $data                    [description]
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Registry $registry ,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->_scopeConfig = $context->getScopeConfig();
		$this->_registry = $registry;
	}

    /**
     * @return Product
     */
    private function getProduct()
    {
        if (is_null($this->product)) {
            $this->product = $this->_registry->registry('product');

            if (!$this->product->getId()) {
                throw new LocalizedException(__('Failed to initialize product'));
            }
        }

        return $this->product;
    }

	public function getProductName()
    {
        return $this->getProduct()->getName();
    }

	public function isLocal()
	{
		return $this->_scopeConfig->getValue('socialshare/local/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function showFacebook()
	{
		return $this->_scopeConfig->getValue('socialshare/facebook/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function showFacebookShare()
	{
		return $this->_scopeConfig->getValue('socialshare/facebook/share', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getFacebookAppId()
	{
		return $this->_scopeConfig->getValue('socialshare/facebook/appid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getFacebookCount()
	{
		return $this->_scopeConfig->getValue('socialshare/facebook/count', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function showTwitter()
	{
		return $this->_scopeConfig->getValue('socialshare/twitter/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function showPinterest()
	{
		return $this->_scopeConfig->getValue('socialshare/pinterest/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function showGPlus()
	{
		return $this->_scopeConfig->getValue('socialshare/google/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getGPlusCount()
	{
		return $this->_scopeConfig->getValue('socialshare/google/count', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
}
