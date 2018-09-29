<?php
namespace Silk\Review\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if ( version_compare($context->getVersion() , '1.0.1') < 0 ) {
            $review = $setup->getTable('review');
            $setup->getConnection()->addColumn(
                $review,
                'score',
                [
                    'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'  => 1,
                    'comment' => 'score'
                ]
            );
        }
        $setup->endSetup();
    }

}