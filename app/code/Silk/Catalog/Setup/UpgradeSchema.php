<?php
namespace Silk\Catalog\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup , ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion() , '1.0.2') < 0) {
            $category = $setup->getTable('catalog_category_entity');
            $setup->getConnection()->addColumn(
                $category,
                'is_show',
                [
                    'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length'  => 1,
                    'default' => 0,
                    'comment' => 'Is Show'
                ]
            );
        }
        if (version_compare($context->getVersion() , '1.0.3') < 0) {
            $category = $setup->getTable('catalog_category_entity');
            $setup->getConnection()->addColumn(
                $category,
                'waterfall_position',
                [
                    'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'  => 120,
                    'default' => null,
                    'comment' => 'Category Waterfall Position'
                ]
            );
        }
        if (version_compare($context->getVersion() , '1.0.4') < 0) {
            $eav = $setup->getTable('eav_attribute');
            $setup->getConnection()->addColumn(
                $eav,
                'attribute_note',
                [
                    'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'  => 300,
                    'default' => null,
                    'comment' => 'attribute note'
                ]
            );
        }
        $setup->endSetup();
    }
}