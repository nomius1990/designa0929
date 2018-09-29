<?php
/**
 * Created by PhpStorm.
 * User: Nate Gan
 * Email nate.gan@silksoftware.com
 * Date: 2017/10/12
 */

namespace Silk\Customer\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory ,
        AttributeSetFactory $attributeSetFactory
    )
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup , ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        if ( version_compare($context->getVersion() , '1.0.2' , '<') ) {
            $attributes = [
                /*using for dealer*/
                'is_enterprise' => [
                    'type' => 'int' ,
                    'label' => 'Is Enterprise' ,
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean' ,
                    'input' => 'select' ,
                    'required' => false ,
                    'visible' => true ,
                    'user_defined' => true ,
                    'position' => 998 ,
                    'sort_order' => 998 ,
                    'default' => 0 ,
                    'system' => 0 ,
                    'note' => 'Is it Enterprise?'
                ] ,

                'er_phone' => [
                    'type' => 'varchar' ,
                    'label' => 'Phone' ,
                    'input' => 'text' ,
                    'unique' => false ,
                    'required' => false ,
                    'visible' => true ,
                    'default' => '' ,
                    'user_defined' => true ,
                    'position' => 999 ,
                    'sort_order' => 999 ,
                    'system' => 0 ,
                    'note' => 'Enterprise Phone' ,
                ] ,

                /*using for customer */
                'er_company' => [
                    'type' => 'varchar' ,
                    'label' => 'Company' ,
                    'input' => 'text' ,
                    'required' => false ,
                    'visible' => true ,
                    'user_defined' => true ,
                    'position' => 1001 ,
                    'sort_order' => 1001 ,
                    'note' => 'Enterprise Company' ,
                    'system' => 0 ,
                ] ,
            ];

            foreach ( $attributes as $attributeCode => $_attribute ) {
                $customerSetup->addAttribute(Customer::ENTITY , $attributeCode , $_attribute);
                $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY , $attributeCode)
                    ->addData([
                        'attribute_set_id' => $attributeSetId ,
                        'attribute_group_id' => $attributeGroupId ,
                        'used_in_forms' => ['adminhtml_customer','customer_account_create','customer_account_edit']
                    ]);
                $attribute->save();
            }
        }
        $setup->endSetup();
    }
}