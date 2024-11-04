<?php

namespace Training\Myapi\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $connection = $setup->getConnection();
            $tableName = $setup->getTable('custom_table');

            if ($connection->isTableExists($tableName) == true) {
                $connection->addColumn(
                    $tableName,
                    'description',
                    [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Description'
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}
