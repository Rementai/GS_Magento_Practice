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

namespace GS\OrderAttribute\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;

class OrderSaveObserver implements ObserverInterface
{
    public const SKU_SEPARATOR = ',';

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getOrder();
        if (!($order instanceof OrderInterface)) {
            return;
        }

        $order->setProductSkus(implode(self::SKU_SEPARATOR, $this->prepareProductSkus($order)));
    }

    /**
     * Prepare product SKUs for the order
     *
     * @param OrderInterface $order
     * @return string[]
     */
    private function prepareProductSkus(OrderInterface $order): array
    {
        $skus = [];
        foreach ($order->getAllItems() as $item) {
            $skus[] = $item->getSku();
        }

        return $skus;
    }
}
