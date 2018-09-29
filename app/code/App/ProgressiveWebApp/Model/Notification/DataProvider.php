<?php
/**
 * @copyright Copyright
 */

namespace App\ProgressiveWebApp\Model\Notification;

use App\ProgressiveWebApp\Model\ResourceModel\Notification\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $notificationCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $notificationCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $notificationCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $notification) {
            $notificationData = $notification->getData();
            $iconData = unserialize($notification['icon']);
            if (!empty($iconData)) {
                $notificationData['icon'] = [];
                $notificationData['icon'][0]['name'] = $iconData[0]['name'];
                $notificationData['icon'][0]['url'] = $iconData[0]['url'];
            }
            $this->loadedData[$notification->getId()]['notification'] = $notificationData;
        }
        return $this->loadedData;
    }
}
