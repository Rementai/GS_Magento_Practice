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

namespace GS\OrderAttribute\Block\Order;

use GS\OrderAttribute\Observer\OrderSaveObserver;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class SkuList extends Template
{
    public const ENCODING_UTF8 = 'UTF-8';

    public Escaper $escaper;
    /** @var string[] $productSkus */
    private array $productSkus = [];

    /**
     * Constructor
     *
     * @param Context $context
     * @param Escaper $escaper
     * @param string[]|mixed[] $data
     */
    public function __construct(
        Context $context,
        Escaper $escaper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->escaper = $escaper;
    }

    /**
     * Set product SKUs.
     *
     * @param string[] $productSkus
     * @return $this
     */
    public function setProductSkus(array $productSkus): self
    {
        $this->productSkus = $productSkus;

        return $this;
    }

    /**
     * Get product SKUs.
     *
     * @return string[]
     */
    public function getProductSkus(): array
    {
        return $this->productSkus;
    }

    /**
     * Get SKU list as a comma-separated string.
     *
     * @return string
     */
    public function getSkuList(): string
    {
        return implode(OrderSaveObserver::SKU_SEPARATOR, array_map('trim', $this->productSkus));
    }
}
