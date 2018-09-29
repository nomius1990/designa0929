<?php
/**
 * @copyright Copyright
 */

namespace App\ProgressiveWebApp\Controller\Adminhtml\Notification;

use App\ProgressiveWebApp\PhpFirebaseCloudMessaging\Client;
use App\ProgressiveWebApp\PhpFirebaseCloudMessaging\Message;
use App\ProgressiveWebApp\PhpFirebaseCloudMessaging\Recipient\Topic;
use App\ProgressiveWebApp\PhpFirebaseCloudMessaging\Notification;
use Magento\Backend\App\Action;

class SendNotification extends \Magento\Backend\App\Action
{
    protected $scope;
    protected $_client;
    protected $_message;
    protected $_notification;
    protected $_pwaHelper;
    protected $_notificationFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        Client $client,
        Message $message,
        \App\ProgressiveWebApp\Helper\Data $helper,
        \App\ProgressiveWebApp\Model\NotificationFactory $notificationFactory
    ) {
        $this->scope = $scope;
        $this->_client = $client;
        $this->_message = $message;
        $this->_pwaHelper = $helper;
        $this->_notificationFactory = $notificationFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $notificationId = $this->getRequest()->getParam('notification_id');
        $notification = $this->_notificationFactory->create()->load($notificationId);
        $notificationData = $notification->getData();
        if (!empty($notificationData)) {
            $icon = unserialize($notificationData['icon']);
            $server_key = $this->_pwaHelper->getServerKey();
            $topicName = $this->_pwaHelper->getTopicName();
            $this->_client->setApiKey($server_key);
            $this->_notification = new Notification($notificationData['title'], $notificationData['body']);
            $this->_message->addRecipient(new Topic($topicName));
            $this->_message->setNotification($this->_notification);
            $this->_notification->setIcon($icon[0]['url']);
            $this->_notification->setClickAction($notificationData['target_url']);
            try {
                $send = $this->_client->send($this->_message);
                $reponse = $send->getStatusCode();
                if ($reponse == 200) {
                    $this->messageManager->addSuccess(__('Send notification successfully.'));
                    return $resultRedirect->setPath('*/*/');
                } else {
                    $this->messageManager->addError(__('Send notification failed. Please try again.'));
                    return $resultRedirect->setPath('*/*/');
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while sending notification.'));
            }
        } else {
            $this->messageManager->addError( __('Cannot send the notification. The notification does not exists.'));
        }
        return $resultRedirect->setPath('*/*/');
    }
}