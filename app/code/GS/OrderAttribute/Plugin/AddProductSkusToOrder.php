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

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class AddProductSkusToOrder
{
    public const PRODUCT_SKUS_ATTRIBUTE = 'product_skus';

    /**
     * Constructor
     *
     * @param OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        protected OrderExtensionFactory $orderExtensionFactory
    ) {
    }

    /**
     * Adds 'product_skus' attribute to order after it's fetched
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order): OrderInterface
    {
        $this->addProductSkusAttribute($order);

        return $order;
    }

    /**
     * Adds 'product_skus' attribute to each order in search result after it's fetched
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     * @return OrderSearchResultInterface
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $searchResult
    ): OrderSearchResultInterface {
        foreach ($searchResult->getItems() as $order) {
            $this->addProductSkusAttribute($order);
        }

        return $searchResult;
    }

    /**
     * Adds 'product_skus' attribute to each order extension attributes.
     *
     * @param OrderInterface $order
     * @return void
     */
    private function addProductSkusAttribute(OrderInterface $order): void
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }

        $extensionAttributes->setProductSkus($order->getData(AddProductSkusToOrder::PRODUCT_SKUS_ATTRIBUTE));
        $order->setExtensionAttributes($extensionAttributes);
    }
}
