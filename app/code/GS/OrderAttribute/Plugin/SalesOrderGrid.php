<?php

declare(strict_types=1);

/**
 * GS OrderAttribute
 *
 * @copyright Copyright (c) 2024 Gate-Software Sp. z o.o. (www.gate-software.com). All rights reserved.
 * @author    Gate-Software Practice Team
 * @author    sebastian.mazgaj@gate-commerce.com
 *
 * @package   GS_OrderAttribute
 */

namespace GS\OrderAttribute\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;

class SalesOrderGrid
{
    /**
     * Modify order admin grid before it's loaded.
     *
     * @param Collection $subject
     * @return null
     * @throws LocalizedException
     */
    public function beforeLoad(Collection $subject): void
    {
        if ($subject->isLoaded()) {
            return;
        }

        $primaryKey = $subject->getResource()->getIdFieldName();
        $tableName = $subject->getConnection()->getTableName('sales_order');

        $subject->getSelect()->joinLeft(
            $tableName,
            $tableName . '.entity_id = main_table.' . $primaryKey,
            [AddProductSkusToOrder::PRODUCT_SKUS_ATTRIBUTE]
        );
    }
}
