<?php

declare(strict_types=1);

/**
 * GS PhoneLogin
 *
 * @copyright Copyright (c) 2024 Gate-Software Sp. z o.o. (www.gate-software.com). All rights reserved.
 * @author    Gate-Software Practice Team
 * @author    sebastian.mazgaj@gate-commerce.com
 *
 * @package   GS_PhoneLogin
 */

namespace GS\PhoneLogin\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private const CUSTOMER_ENTITY = 'customer';
    private const ATTRIBUTE_CODE = 'phone_number';
    private const ATTRIBUTE_TYPE = 'varchar';
    private const ATTRIBUTE_LABEL = 'Phone Number';
    private const ATTRIBUTE_INPUT = 'text';
    private const ATTRIBUTE_POSITION = 1000;
    private const USED_IN_FORMS = [
        'adminhtml_customer',
        'customer_account_create',
        'customer_account_edit',
    ];

    /**
     * InstallData constructor
     *
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        private CustomerSetupFactory $customerSetupFactory,
        private AttributeSetFactory $attributeSetFactory
    ) {
    }

    /**
     * Installs new EAV attribute
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context): void
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType(self::CUSTOMER_ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(self::CUSTOMER_ENTITY, self::ATTRIBUTE_CODE, [
            'type' => self::ATTRIBUTE_TYPE,
            'label' => self::ATTRIBUTE_LABEL,
            'input' => self::ATTRIBUTE_INPUT,
            'required' => true,
            'unique' => true,
            'position' => self::ATTRIBUTE_POSITION,
            'system' => false,
        ]);

        $customerSetup->getEavConfig()
            ->getAttribute(self::CUSTOMER_ENTITY, self::ATTRIBUTE_CODE)
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => self::USED_IN_FORMS,
            ])
            ->save();

    }
}
