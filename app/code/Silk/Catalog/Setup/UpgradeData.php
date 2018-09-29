<?php

namespace Silk\Catalog\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{

    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        if ($context->getVersion()
            && version_compare($context->getVersion(), '1.0.3') < 0
        ) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'image_banner', [
                    'type' => 'varchar',
                    'label' => 'Category Banner',
                    'input' => 'image',
                    'required' => false,
                    'sort_order' => 6,
                    'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                ]
            );
        }
        if ($context->getVersion()
            && version_compare($context->getVersion(), '1.0.5') < 0
        ) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'enable_link', [
                    'type'         => 'varchar',
                    'label'        => 'Enable Link',
                    'input'        => 'select',
                    'sort_order'   => 100,
                    'source'       => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'default'      => 0,
                    'visible'      => true,
                    'required'     => false,
                    'user_defined' => false,
                    'group'        => 'General Information',
                ]
            );
        }
        if ($context->getVersion()
            && version_compare($context->getVersion(), '1.0.6') < 0
        ) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'navagation_image', [
                    'type' => 'varchar',
                    'label' => 'Navagation Image',
                    'input' => 'image',
                    'required' => false,
                    'sort_order' => 6,
                    'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                ]
            );
        }
        $setup->endSetup();
    }
}
