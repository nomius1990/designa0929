<?php
namespace Silk\Import\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\Math\Random;
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csv;
    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerDataFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var AddressInterfaceFactory
     */
    protected $addressDataFactory;
    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;
    /**
     * @var \Magento\Directory\Model\Country
     */
    protected $country;
    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $_regionCollectionFactory;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
    protected $countryData;
    public function __construct(
        Action\Context $context,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\App\ResourceConnection  $resource,
        \Magento\Directory\Model\Country $country,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory
    )
    {
        $this->_resource           = $resource;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->addressDataFactory  = $addressDataFactory;
        $this->customerDataFactory = $customerDataFactory;
        $this->dataObjectHelper    = $dataObjectHelper;
        $this->country             = $country;
        $this->_regionCollectionFactory = $regionCollectionFactory;
        $this->csv = $csv;
        parent::__construct($context);
    }
    public function execute()
    {
        if (isset($_FILES['customer']['tmp_name']) && !empty($_FILES['customer']['tmp_name']))
        {
            $csvData = $this->csv->getData($_FILES['customer']['tmp_name']);
            $result  = $this->importCustomer($csvData);
        }
        if (isset($_FILES['order']['tmp_name']) && !empty($_FILES['order']['tmp_name']))
        {
            $csvData = $this->csv->getData($_FILES['order']['tmp_name']);
            $result  = $this->importOrder($csvData);
        }

    }
    public function importCustomer($csvData)
    {
        if (is_array($csvData) && !empty($csvData)) {
            foreach ($csvData as $row => $v) {
                if ($row > 0) {
                    //将客户数据存入数据库
                    $data   = [];
                    $region = [];
                    $data['customer']['firstname']      = $v[1];
                    $data['customer']['lastname']       = $v[2];
                    $data['customer']['email']          = $v[0];
                    $data['customer']['disable_auto_group_change'] = 0;
                    $data['customer']['confirmation']   = false;
                    //enterprise
                    $data['customer']['is_enterprise']  = 0;//1是,0不是
                    $data['customer']['er_company']     = '';
                    $data['customer']['er_phone']       = '';

                    $data['customer']['group_id']       = 1;//1为默认
                    //$data['customer']['gender']         = 1;//1为男
                    $data['customer']['sendemail_store_id']    = 1;
                    $data['customer']['website_id']     = 1;
                    //$data['customer']['dob']            = '8/14/2018';
                    //先导入address
                    $data['address']['new_0']['firstname']     = $v[9];
                    $data['address']['new_0']['lastname']      = $v[10];
                    $data['address']['new_0']['company']       = $v[11];
                    $data['address']['new_0']['city']          = $v[14];
                    $data['address']['new_0']['telephone']     = $v[18];
                    $data['address']['new_0']['street'][0]     = $v[12];
                    $data['address']['new_0']['street'][1]     = $v[13];
                    $data['address']['new_0']['default_billing']      = true;
                    $data['address']['new_0']['default_shipping']     = true;
                    $data['address']['new_0']['postcode']      = $v[16];
                    $data['address']['new_0']['country_id']    = $this->getCountryCode($v[17]);
                    $region = $this->getRegion($v[15],$data['address']['new_0']['country_id']);
                    $data['address']['new_0']['region_id']     = $region['region_id'];
                    $data['address']['new_0']['region']        = $region['region'];

                    //然后导入客户信息
                    /** @var CustomerInterface $customer */
                    $customer     = $this->customerDataFactory->create();
                    $customerData = $data['customer'];
                    $addressesData[]      = $data['address']['new_0'];
                    $this->dataObjectHelper->populateWithArray(
                        $customer,
                        $customerData,
                        \Magento\Customer\Api\Data\CustomerInterface::class
                    );
                    $addresses = [];
                    foreach ($addressesData as $addressData) {
                        $region = isset($addressData['region']) ? $addressData['region'] : null;
                        $regionId = isset($addressData['region_id']) ? $addressData['region_id'] : null;
                        $addressData['region'] = [
                            'region' => $region,
                            'region_id' => $regionId,
                        ];
                        $addressDataObject = $this->addressDataFactory->create();
                        $this->dataObjectHelper->populateWithArray(
                            $addressDataObject,
                            $addressData,
                            \Magento\Customer\Api\Data\AddressInterface::class
                        );
                        $addresses[] = $addressDataObject;
                    }

                    $this->_eventManager->dispatch(
                        'adminhtml_customer_prepare_save',
                        ['customer' => $customer, 'request' => $this->getRequest()]
                    );
                    $customer->setAddresses($addresses);
                    if (isset($customerData['sendemail_store_id'])) {
                        $customer->setStoreId($customerData['sendemail_store_id']);
                    }
                    $password = $this->getPassword();
                    $customer = $this->customerAccountManagement->createAccount($customer,$password);
                    $customerId = $customer->getId();
                }
            }
        }
    }
    public function getRegion($regionName,$countryId)
    {
        $result = [];
        $collection = $this->_regionCollectionFactory->create();
        $collection->addCountryFilter($countryId);
        if (!empty($collection->getData())) {
            foreach ($collection as $region) {
                if ($region->getName() == $regionName) {
                    $id = $region->getRegionId();
                    break;
                }
            }
            if ($id) {
                $result['region']    = '';
                $result['region_id'] = $id;
            } else {
                $result['region']    = $regionName;
                $result['region_id'] = '';
            }
        } else {
            $result['region']    = $regionName;
            $result['region_id'] = '';
        }
        return $result;
    }
    public function getCountryCode($name)
    {
        if (!empty($this->countryData)) {
            $collection = $this->countryData;
        } else {
            $collection = $this->country->getCollection();
            $this->countryData  = $collection;
        }

        foreach ($collection as $country) {
            if ($name == $country->getName()) {
                $id = $country->getCountryId();break;
            }
        }
        if ($id) {
            return $id;
        } else {
            return 'SB';
        }
    }
    public function getPassword()
    {
        $str1 = 'ABCDEFGHIGKLMNOPQHISTUVWXYZ';
        $str2 = 'abcdefghigklmnopqhistuvwxyz';
        $password = substr($str1,mt_rand(0,22),4).substr($str2,mt_rand(0,22),4).mt_rand(100,999);
        return $password;
    }
    public function importOrder($csvData)
    {var_dump($csvData);die;
        if (is_array($csvData) && !empty($csvData)) {
            foreach ($csvData as $row => $data) {
                if ($row > 0) {
                    //将订单数据存入数据库
                    $collection = $this->_resource->getConnection();
                    //sales_order_address
                    $sales_address = $collection->getTableName('sales_order');
                    $address    = [];
                    $address    = $this->getSalesAddress($data);
                    //sales_order  需要先导order,grid然后再item,address,最后用address的id返回去更改order里的address id
                    $sales_order = $collection->getTableName('sales_order');
                    $info = [];
                    $info['state']             = '';
                    $info['status']            = '';
                    $info['protect_code']      = substr(
                        hash('sha256', uniqid(Random::getRandomNumber(), true) . ':' . microtime(true)),
                        5,
                        32
                    );
                    $info['shipping_description']        = '';
                    $info['is_virtual']                  = 0;
                    $info['store_id']                    = 1;
                    $info['customer_id']                 = '';
                    $info['base_grand_total']            = '';
                    $info['base_shipping_amount']        = '';
                    $info['base_subtotal']               = '';
                    $info['base_to_order_rate']          = 1;
                    $info['base_to_global_rate']         = 1;
                    $info['grand_total']                 = '';
                    $info['shipping_amount']             = '';
                    $info['subtotal']                    = '';
                    $info['total_paid']                  = '';
                    $info['total_qty_ordered']           = '';
                    $info['customer_is_guest']           = '';//1   0
                    $info['customer_group_id']           = '';//根据customer 的group来,没有就是0
                    $info['billing_address_id']          = '';//根据插入order_address来
                    $info['shipping_address_id']         = '';//根据插入order_address来
                    $info['base_subtotal_incl_tax']      = '';
                    $info['subtotal_incl_tax']           = '';
                    //$info['weight']                      = '';
                    $info['increment_id']                = '';
                    $info['base_currency_code']          = 'USD';
                    $info['customer_email']              = '';
                    $info['customer_firstname']          = '';//名字合为一起
                    $info['global_currency_code']        = 'USD';
                    $info['order_currency_code']         = '';//实际货币
                    $info['shipping_method']             = '';
                    $info['store_currency_code']         = 'USD';
                    $info['store_name']                  = 'Main Website';
                    $info['created_at']                  = '';
                    $info['updated_at']                  = '';//同上
                    $info['total_item_count']            = '';//总数量
                    $info['shipping_incl_tax']           = '';//同运费一样
                    $info['base_shipping_incl_tax']      = '';//同运费一样
                    $collection->insert($sales_order,$info);
                    //sales_order_item

                    //
                }
            }
        }
    }
    public function getSalesAddress($data)
    {
        $array = [];
        $array['billing']['parent_id']               = '';//order id
        $array['billing']['region_id']               = '';
        $array['billing']['region']                  = '';
        $array['billing']['postcode']                = '';
        $array['billing']['lastname']                = '';
        $array['billing']['street']                  = '';
        $array['billing']['city']                    = '';
        $array['billing']['email']                   = '';
        $array['billing']['telephone']               = '';
        $array['billing']['country_id']              = '';
        $array['billing']['firstname']               = '';
        $array['billing']['address_type']            = 'billing';
        $array['billing']['company']                 = '';

        $array['shipping']['parent_id']               = '';//order id
        $array['shipping']['region_id']               = '';
        $array['shipping']['region']                  = '';
        $array['shipping']['postcode']                = '';
        $array['shipping']['lastname']                = '';
        $array['shipping']['street']                  = '';
        $array['shipping']['city']                    = '';
        $array['shipping']['email']                   = '';
        $array['shipping']['telephone']               = '';
        $array['shipping']['country_id']              = '';
        $array['shipping']['firstname']               = '';
        $array['shipping']['address_type']            = 'shipping';
        $array['shipping']['company']                 = '';

        return $array;
    }
    public function getSalesOrderGrid($data)
    {
        $array = [];
        $array['status']                 = '';
        $array['store_id']               = 1;
        $array['store_name']             = 'Main Website';
        $array['customer_id']            = '';//需要根据customer name查找
        $array['base_grand_total']       = '';
        $array['base_total_paid']        = '';
        $array['grand_total']            = '';
        $array['total_paid']             = '';
        $array['increment_id']           = '';
        $array['base_currency_code']     = 'USD';
        $array['order_currency_code']    = '';
        $array['shipping_name']          = '';
        $array['billing_name']           = '';
        $array['created_at']             = '';
        $array['updated_at']             = '';
        $array['billing_address']        = '';
        $array['shipping_address']       = '';
        $array['shipping_information']   = '';
        $array['customer_email']         = '';
        $array['customer_group']         = '';//0为guest
        $array['subtotal']               = '';
        $array['shipping_and_handling']  = '';
        $array['customer_name']          = '';//没有就为空
        $array['payment_method']         = '';
        return $array;
    }
    public function getSalesOrderItem($data)
    {
        $array = [];
        $array['order_id']               = '';
        $array['store_id']               = 1;
        $array['created_at']             = '';
        $array['updated_at']             = '';
        $array['sku']                    = '';
        $array['name']                   = '';
        $array['price']                  = '';
        $array['base_price']             = '';
        $array['original_price']         = '';
        $array['base_original_price']    = '';
        $array['row_total']              = '';
        $array['base_row_total']         = '';
        $array['price_incl_tax']         = '';
        $array['base_price_incl_tax']    = '';
        $array['row_total_incl_tax']     = '';
        $array['base_row_total_incl_tax']       = '';
        return $array;
    }
}