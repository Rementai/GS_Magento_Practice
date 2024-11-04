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

namespace GS\PhoneLogin\Model;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;

class LoginByTelephone
{
    private const ATTR_EMAIL = 'email';
    private const ATTR_PHONE_NUMBER = 'phone_number';
    private const ATTR_ENTITY_ID = 'entity_id';
    private const FILTER_EQUAL = 'eq';
    private const PAGE_SIZE = 1;
    private const CUR_PAGE = 1;


    /**
     * LoginByTelephone constructor
     *
     * @param CollectionFactory $customerCollectionFactory
     */
    public function __construct(
        protected CollectionFactory $customerCollectionFactory
    ) {
    }

    /**
     * Authenticate customer by telephone
     *
     * @param string $telephone
     * @return string|false
     * @throws LocalizedException
     */
    public function authenticateByTelephone(string $telephone): bool|string
    {
        $collection = $this->customerCollectionFactory->create();
        $collection->addAttributeToSelect([self::ATTR_EMAIL, self::ATTR_PHONE_NUMBER, self::ATTR_ENTITY_ID])
            ->addAttributeToFilter(self::ATTR_PHONE_NUMBER, [self::FILTER_EQUAL => $telephone])
            ->setPageSize(self::PAGE_SIZE)
            ->setCurPage(self::CUR_PAGE);

        if ($collection->count() >= 1) {
            return $collection->getFirstItem()->getData(self::ATTR_EMAIL);
        }

        return false;
    }
}
