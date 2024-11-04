<?php

declare(strict_types=1);

/**
 * GS GuestBook
 *
 * @copyright Copyright (c) 2024 Gate-Software Sp. z o.o. (www.gate-software.com). All rights reserved.
 * @author    Gate-Software Practice Team
 * @author    sebastian.mazgaj@gate-commerce.com
 *
 * @package   GS_GuestBook
 */

namespace GS\GuestBook\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class Form extends Template
{
    public const FIELD_FIRST_NAME = 'first_name';
    public const FIELD_LAST_NAME = 'last_name';
    public const FIELD_EMAIL = 'email';
    public const FIELD_CONSENT = 'consent';
    public const MAX_LENGTH_NAME = 50;
    public const MAX_LENGTH_EMAIL = 100;
    private const FORM_ACTION_URL = 'guestbook/index/post';
    private const HEADER_TEXT_CONFIG_PATH = 'design/guestbook_design/header_text';
    private const BUTTON_COLOR_CONFIG_PATH = 'design/guestbook_design/button_color';
    private const FORM_BACKGROUND_COLOR_CONFIG_PATH = 'design/guestbook_design/form_background_color';
    private const FIRST_NAME_LABEL_CONFIG_PATH = 'design/guestbook_design/first_name_label';
    private const LAST_NAME_LABEL_CONFIG_PATH = 'design/guestbook_design/last_name_label';
    private const EMAIL_LABEL_CONFIG_PATH = 'design/guestbook_design/email_label';
    private const CONSENT_LABEL_CONFIG_PATH = 'design/guestbook_design/consent_label';
    private const DEFAULT_HEADER_TEXT = 'Guestbook';
    private const DEFAULT_BUTTON_COLOR = '#007bff';
    private const DEFAULT_FORM_BACKGROUND_COLOR = '#f9f9f9';
    private const DEFAULT_FIRST_NAME_LABEL = 'First Name';
    private const DEFAULT_LAST_NAME_LABEL = 'Last Name';
    private const DEFAULT_EMAIL_LABEL = 'Email';
    private const DEFAULT_CONSENT_LABEL = 'I agree to the terms';

    private ScopeConfigInterface $scopeConfig;
    private Escaper $escaper;

    /**
     * Form constructor
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Escaper $escaper
     * @param array<string, mixed> $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Escaper $escaper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->escaper = $escaper;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get escaper instance
     *
     * @return Escaper
     */
    public function getEscaper(): Escaper
    {
        return $this->escaper;
    }

    /**
     * Get action URL for form
     *
     * @return string
     */
    public function getFormAction(): string
    {
        return $this->getUrl(self::FORM_ACTION_URL, ['_secure' => true]);
    }

    /**
     * Get header text from configuration tab
     *
     * @return string|null
     */
    public function getHeaderText(): ?string
    {
        return $this->getConfigValue(self::HEADER_TEXT_CONFIG_PATH, self::DEFAULT_HEADER_TEXT);
    }

    /**
     * Get header text from configuration tab
     *
     * @return string|null
     */
    public function getButtonColor(): ?string
    {
        return $this->getConfigValue(self::BUTTON_COLOR_CONFIG_PATH, self::DEFAULT_BUTTON_COLOR);
    }

    /**
     * Get form background color from configuration tab
     *
     * @return string|null
     */
    public function getFormBackgroundColor(): ?string
    {
        return $this->getConfigValue(self::FORM_BACKGROUND_COLOR_CONFIG_PATH, self::DEFAULT_FORM_BACKGROUND_COLOR);
    }

    /**
     * Get first name placeholder from the configuration tab
     *
     * @return string|null
     */
    public function getFirstNameLabel(): ?string
    {
        return $this->getConfigValue(self::FIRST_NAME_LABEL_CONFIG_PATH, self::DEFAULT_FIRST_NAME_LABEL);
    }

    /**
     * Get last name placeholder from configuration tab
     *
     * @return string|null
     */
    public function getLastNameLabel(): ?string
    {
        return $this->getConfigValue(self::LAST_NAME_LABEL_CONFIG_PATH, self::DEFAULT_LAST_NAME_LABEL);
    }

    /**
     * Get email placeholder from configuration tab
     *
     * @return string|null
     */
    public function getEmailLabel(): ?string
    {
        return $this->getConfigValue(self::EMAIL_LABEL_CONFIG_PATH, self::DEFAULT_EMAIL_LABEL);
    }

    /**
     * Get consent text from configuration tab
     *
     * @return string|null
     */
    public function getConsentLabel(): ?string
    {
        return $this->getConfigValue(self::CONSENT_LABEL_CONFIG_PATH, self::DEFAULT_CONSENT_LABEL);
    }

    /**
     * Helper method to get config value that checks if it's empty
     *
     * @param string $path
     * @param string|null $defaultValue
     * @return string|null
     */
    private function getConfigValue(string $path, ?string $defaultValue = null): ?string
    {
        return $this->readConfigValue($path, $defaultValue);
    }

    /**
     * Reads configuration value
     *
     * @param string $path
     * @param string|null $defaultValue
     * @return string|null
     */
    private function readConfigValue(string $path, ?string $defaultValue = null): ?string
    {
        $value = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);

        return $value !== null && trim($value) !== '' ? $value : $defaultValue;
    }
}
