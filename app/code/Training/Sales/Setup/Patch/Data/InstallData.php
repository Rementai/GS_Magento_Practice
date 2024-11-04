<?php

namespace Training\Sales\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallData implements DataPatchInterface
{
    private $moduleDataSetup;

    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $data = [
            ['name' => 'Product 1', 'price' => 10.00],
            ['name' => 'Product 2', 'price' => 20.00],
            ['name' => 'Product 3', 'price' => 30.00],
            ['name' => 'Product 4', 'price' => 40.00],
            ['name' => 'Product 5', 'price' => 50.00],
        ];

        $this->moduleDataSetup->getConnection()->insertMultiple('training_sales_table', $data);

        $this->moduleDataSetup->endSetup();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
