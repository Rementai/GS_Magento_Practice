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

namespace GS\GuestBook\Model;

use GS\GuestBook\Api\Data\EntryInterface;
use Magento\Framework\Model\AbstractModel;

class Entry extends AbstractModel implements EntryInterface
{
    /**
     * Initialize  model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel\EntryResource::class);
    }

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->_getData(self::ENTRY_ID) ? (int)$this->_getData(self::ENTRY_ID) : null;
    }

    /**
     * Set id
     *
     * @param mixed $value
     * @return void
     */
    public function setId(mixed $value): void
    {
        $this->setData(self::ENTRY_ID, (int)$value);
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->_getData(self::FIRST_NAME);
    }

    /**
     * Set first name
     *
     * @param string $firstName
     * @return void
     */
    public function setFirstName(string $firstName): void
    {
        $this->setData(self::FIRST_NAME, $firstName);
    }

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->_getData(self::LAST_NAME);
    }

    /**
     * Set last name
     *
     * @param string $lastName
     * @return void
     */
    public function setLastName(string $lastName): void
    {
        $this->setData(self::LAST_NAME, $lastName);
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->_getData(self::EMAIL);
    }

    /**
     * Set email
     *
     * @param string $email
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->setData(self::EMAIL, $email);
    }

    /**
     * Get IP
     *
     * @return string
     */
    public function getIpAddress(): ?string
    {
        return $this->_getData(self::IP_ADDRESS);
    }

    /**
     * Set IP
     *
     * @param string $ipAddress
     * @return void
     */
    public function setIpAddress(string $ipAddress): void
    {
        $this->setData(self::IP_ADDRESS, $ipAddress);
    }
}
