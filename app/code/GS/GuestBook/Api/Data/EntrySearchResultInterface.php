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

use Magento\Framework\Api\SearchResultsInterface;

interface EntrySearchResultInterface extends SearchResultsInterface
{
    /**
     * Get the list of guestbook entries
     *
     * @return \GS\GuestBook\Api\Data\EntryInterface[]
     */
    public function getItems();

    /**
     * Set the list of guestbook entries
     *
     * @param \GS\GuestBook\Api\Data\EntryInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
