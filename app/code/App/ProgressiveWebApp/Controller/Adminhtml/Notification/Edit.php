<?php
/**
 * @copyright Copyright
 */

namespace App\ProgressiveWebApp\Controller\Adminhtml\Notification;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
    }

    /**
     * Product edit form
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('notification_id');
        $notification = $this->_objectManager->create('App\ProgressiveWebApp\Model\Notification');
        if ($id) {
            $this->_coreRegistry->register('current_notification_id', $id);
            $notification->load($id);
            if (!$notification->getId()) {
                $this->messageManager->addError(__('This notification no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Notification') : __('New Notification'), $id ? __('Edit Notification') : __('New Notification')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Notifications'));
        $resultPage->getConfig()->getTitle()
            ->prepend($notification->getId() ? __('Edit ') . $notification->getTitle() : __('New Notification'));
        return $resultPage;
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('App_Dailydeal::pwa')
            ->addBreadcrumb(__('Notifications'), __('Notifications'))
            ->addBreadcrumb(__('Manage Notifications'), __('Manage Notifications'));
        return $resultPage;
    }
}
