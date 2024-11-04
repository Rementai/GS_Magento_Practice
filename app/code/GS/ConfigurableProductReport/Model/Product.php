<?php

declare(strict_types=1);

/**
 * GS ConfigurableProductReport
 *
 * @copyright Copyright (c) 2024 Gate-Software Sp. z o.o. (www.gate-software.com). All rights reserved.
 * @author    Gate-Software Practice Team
 * @author    sebastian.mazgaj@gate-commerce.com
 *
 * @package   GS_ConfigurableProductReport
 */

namespace GS\ConfigurableProductReport\Model;

use Magento\Catalog\Model\Product as MagentoProduct;

class Product extends MagentoProduct
{
    public const ATTRIBUTE_TYPE_ID = 'type_id';
    public const TYPE_CONFIGURABLE = 'configurable';
}
