<?php
/**
 * Landofcoder
 * 
 */
namespace Lof\All\Block\Adminhtml;

class License extends \Magento\Framework\View\Element\Template
{
	protected function _toHtml() {
		$extension = $this->getData('extension');
		if ($extension) {
			$this->_eventManager->dispatch(
				'lof_check_license',
				['obj' => $this,'ex'=>$extension]
				);
			$extension = str_replace("_", " ", $extension);
			if (!$this->getData('is_valid')) {
				return '<div style="margin-top: 5px;"><div class="messages error"><div class="message message-error" style="margin-bottom: 0;"><div>Module <b>' . $extension . '</b> is not yet registered! Go to <b>Backend > Landofcoder > Licenses</b> to register the module. Please login to your account in <a target="_blank" href="https://landofcoder.com">landofcoder.com</a>, then go to <b>Dashboard > My Downloadable Products</b>, enter your domains to get a new license. Next go to <b>Backend > Landofcoder > Licenses</b> to save the license.</div></div></div></div>';
	        }
	    }
		return parent::_toHtml();
	}
}