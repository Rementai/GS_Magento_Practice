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

use GS\OrderAttribute\Block\Order\SkuList;
use GS\OrderAttribute\Observer\OrderSaveObserver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Block\Adminhtml\Order\View as OrderViewBlock;
use Magento\Sales\Model\Order;

class OrderView
{
    public const TEMPLATE = 'GS_OrderAttribute::order/sku_list.phtml';
    private const SESSION_KEY_PREFIX = 'order_sku_added_';

    /**
     * Constructor
     *
     * @param ResourceConnection $resourceConnection
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param TemplateContext $context
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        private ResourceConnection $resourceConnection,
        private RequestInterface $request,
        private ResponseInterface $response,
        private TemplateContext $context,
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    /**
     * Adds product SKUs to order view
     *
     * @param OrderViewBlock $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(OrderViewBlock $subject, string $result): string
    {
        $order = $subject->getOrder();
        if (!($order instanceof Order)) {
            return $result;
        }

        $orderId = (int)$order->getId();
        $productSkus = $this->getProductSkusFromOrder($orderId);
        if (empty($productSkus)) {
            return $result;
        }

        $sessionKey = self::SESSION_KEY_PREFIX . $orderId;
        if ($this->request->getParam($sessionKey)) {
            return $result;
        }

        $block = $this->context->getLayout()->createBlock(SkuList::class);
        $block->setTemplate(self::TEMPLATE);
        $block->setProductSkus($productSkus);

        $this->request->setParam($sessionKey, true);

        return $result . $block->toHtml();
    }

    /**
     * Retrieve product SKUs from order
     *
     * @param int $orderId
     * @return string[]
     */
    private function getProductSkusFromOrder(int $orderId): array
    {
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            return [];
        }

        $productSkus = $order->getData(AddProductSkusToOrder::PRODUCT_SKUS_ATTRIBUTE);

        return !empty($productSkus) ? explode(OrderSaveObserver::SKU_SEPARATOR, $productSkus) : [];
    }
}
