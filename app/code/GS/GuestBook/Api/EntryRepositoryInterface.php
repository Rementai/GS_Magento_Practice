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

namespace GS\GuestBook\Api;

use GS\GuestBook\Api\Data\EntryInterface;
use GS\GuestBook\Api\Data\EntrySearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface EntryRepositoryInterface
{
    /**
     * Retrieve guestbook entry by ID
     *
     * @param int $id
     * @return \GS\GuestBook\Api\Data\EntryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): EntryInterface;

    /**
     * Save guestbook entry
     *
     * @param \GS\GuestBook\Api\Data\EntryInterface $entry
     * @return \GS\GuestBook\Api\Data\EntryInterface
     */
    public function save(EntryInterface $entry): EntryInterface;

    /**
     * Delete guestbook entry by ID
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

    /**
     * Retrieve a list of guestbook entries based on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \GS\GuestBook\Api\Data\EntrySearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): EntrySearchResultInterface;
}
