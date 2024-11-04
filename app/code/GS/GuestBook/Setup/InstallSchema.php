<?php

declare(strict_types=1);

/**
 * GS GuestBook
 *
 * @copyright Copyright (c) 2024 Gate-Software Sp. z o.o. (www.gate-software.com). All rights reserved.
 * @author    Gate-Software Practice Team
 * @author    sebastian.mazgaj@gate-commerce.com
 *
 * @package   GS_GuestBook
 */

namespace GS\GuestBook\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

class InstallSchema implements InstallSchemaInterface
{
    private const TABLE_NAME = 'guestbook_entries';
    private const COLUMN_ENTRY_ID = 'entry_id';
    private const COLUMN_FIRST_NAME = 'first_name';
    private const COLUMN_LAST_NAME = 'last_name';
    private const COLUMN_EMAIL = 'email';
    private const COLUMN_IP_ADDRESS = 'ip_address';

    /**
     * Installs a new table into the database.
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable($setup->getTable(self::TABLE_NAME))
            ->addColumn(self::COLUMN_ENTRY_ID, Table::TYPE_INTEGER, null, [
                'identity' => true,
                'nullable' => false,
                'primary' => true
            ], 'Entry ID')
            ->addColumn(self::COLUMN_FIRST_NAME, Table::TYPE_TEXT, 255, ['nullable' => false], 'First Name')
            ->addColumn(self::COLUMN_LAST_NAME, Table::TYPE_TEXT, 255, ['nullable' => false], 'Last Name')
            ->addColumn(self::COLUMN_EMAIL, Table::TYPE_TEXT, 255, ['nullable' => false], 'Email')
            ->addColumn(self::COLUMN_IP_ADDRESS, Table::TYPE_TEXT, 255, ['nullable' => false], 'IP Address')
            ->setComment('Guestbook Entries');

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
