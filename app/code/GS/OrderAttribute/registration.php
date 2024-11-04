<?php
/**
 * GS OrderAttribute
 *
 * @copyright Copyright (c) 2024 Gate-Software Sp. z o.o. (www.gate-software.com). All rights reserved.
 * @author    Gate-Software Practice Team
 * @author    sebastian.mazgaj@gate-commerce.com
 *
 * @package   GS_OrderAttribute
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'GS_OrderAttribute',
    __DIR__
);