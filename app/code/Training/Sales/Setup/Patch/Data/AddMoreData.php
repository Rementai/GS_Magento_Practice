<?php

namespace Training\Sales\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AddMoreData implements DataPatchInterface
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
            ['name' => 'Product 6', 'price' => 60.00, 'description' => 'Description 6'],
            ['name' => 'Product 7', 'price' => 70.00, 'description' => 'Description 7'],
            ['name' => 'Product 8', 'price' => 80.00, 'description' => 'Description 8'],
            ['name' => 'Product 9', 'price' => 90.00, 'description' => 'Description 9'],
            ['name' => 'Product 10', 'price' => 100.00, 'description' => 'Description 10'],
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

