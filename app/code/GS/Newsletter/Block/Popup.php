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

namespace GS\Newsletter\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class Popup extends Template
{
    private const CONFIG_ENABLE_POPUP = 'popup/popup_configuration/enable_popup';
    private const CONFIG_POPUP_LOCATION = 'popup/popup_configuration/popup_location';

    /**
     * Checks is popup is active
     *
     */
    public function getPopupActive(): string
    {
        return $this->_scopeConfig->getValue(self::CONFIG_ENABLE_POPUP, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Gets popup location from config
     *
     * @return string
     */
    public function getPopupLocation(): string
    {
        return $this->_scopeConfig->getValue(self::CONFIG_POPUP_LOCATION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Checks if popup should be displayed on site
     *
     * @param string $currentPage
     * @return bool
     */
    public function shouldShowPopup(string $currentPage): bool
    {
        if (!$this->getPopupActive()) {
            return false;
        }

        return $this->getPopupLocation() === $currentPage;
    }
}
