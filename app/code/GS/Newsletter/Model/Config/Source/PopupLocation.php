<?php

declare(strict_types=1);

/**
 * GS Newsletter
 *
 * @copyright Copyright (c) 2024 Gate-Software Sp. z o.o. (www.gate-software.com). All rights reserved.
 * @author    Gate-Software Practice Team
 * @author    sebastian.mazgaj@gate-commerce.com
 *
 * @package   GS_Newsletter
 */

namespace GS\Newsletter\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class PopupLocation implements ArrayInterface
{
    private const LOCATION_HOME = 'home';
    private const LOCATION_PRODUCT = 'product';
    private const LOCATION_CART = 'cart';

    private const LABEL_HOME = 'Homepage';
    private const LABEL_PRODUCT = 'Product Page';
    private const LABEL_CART = 'Cart Page';

    private const PARAM_VALUE = 'value';
    private const PARAM_LABEL = 'label';

    /**
     * Retrieve options for popup location config
     *
     * @return string[]
     */
    public function toOptionArray(): array
    {
        return [
            [self::PARAM_VALUE => self::LOCATION_HOME, self::PARAM_LABEL => __(self::LABEL_HOME)],
            [self::PARAM_VALUE => self::LOCATION_PRODUCT, self::PARAM_LABEL => __(self::LABEL_PRODUCT)],
            [self::PARAM_VALUE => self::LOCATION_CART, self::PARAM_LABEL => __(self::LABEL_CART)],
        ];
    }
}
