<?php
namespace Magestore\Bannerslider\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup , ModuleContextInterface $context)
    {
        $setup->startSetup();
        if ( version_compare($context->getVersion() , '1.7.3') < 0 ) {
            $banner = $setup->getTable('magestore_bannerslider_banner');
            $setup->getConnection()->addColumn(
                $banner,
                'text_color',
                [
                    'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'  => 100,
                    'comment' => 'Banner Text Color'
                ]
            );
        }
        if ( version_compare($context->getVersion() , '1.7.4') < 0 ) {
            $banner = $setup->getTable('magestore_bannerslider_banner');
            $setup->getConnection()->addColumn(
                $banner,
                'image_paid',
                [
                    'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'  => 255,
                    'comment' => 'Pad Banner image'
                ]
            );
            $setup->getConnection()->addColumn(
                $banner,
                'image_mobile',
                [
                    'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'  => 255,
                    'comment' => 'Mobile Banner image'
                ]
            );
        }
        $setup->endSetup();
    }
}