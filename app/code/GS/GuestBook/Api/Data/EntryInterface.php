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

namespace GS\GuestBook\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface EntryInterface extends ExtensibleDataInterface
{
    const ENTRY_ID = 'entry_id';
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';
    const EMAIL = 'email';
    const IP_ADDRESS = 'ip_address';

    /**
     * Get the ID of guestbook entry
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Set the ID of guestbook entry
     *
     * @param mixed $value
     * @return void
     */
    public function setId(mixed $value): void;

    /**
     * Get the first name of guestbook entry author
     *
     * @return string
     */
    public function getFirstName(): ?string;

    /**
     * Set the first name of guestbook entry author
     *
     * @param string $firstName
     * @return void
     */
    public function setFirstName(string $firstName): void;

    /**
     * Get the last name of guestbook entry author
     *
     * @return string
     */
    public function getLastName(): ?string;

    /**
     * Set the last name of guestbook entry author
     *
     * @param string $lastName Last name to set
     * @return void
     */
    public function setLastName(string $lastName): void;

    /**
     * Get the email address of guestbook entry author
     *
     * @return string
     */
    public function getEmail(): ?string;

    /**
     * Set the email address of guestbook entry author
     *
     * @param string $email
     * @return void
     */
    public function setEmail(string $email): void;

    /**
     * Get the IP address of guestbook entry author
     *
     * @return string
     */
    public function getIpAddress(): ?string;

    /**
     * Set the IP address of guestbook entry author
     *
     * @param string $ipAddress
     * @return void
     */
    public function setIpAddress(string $ipAddress): void;
}
