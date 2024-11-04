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

namespace GS\OrderAttribute\Model;

use GS\OrderAttribute\Api\Data\OrderInterface;
use Magento\Sales\Model\Order as OrderModel;

/**
 * @method string|null getProductSkus()
 * @method $this setProductSkus(string $skus)
 */
class Order extends OrderModel implements OrderInterface
{
}
