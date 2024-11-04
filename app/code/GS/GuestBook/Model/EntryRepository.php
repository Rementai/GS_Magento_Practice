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

use Exception;
use GS\GuestBook\Api\Data\EntryInterface;
use GS\GuestBook\Api\Data\EntrySearchResultInterface;
use GS\GuestBook\Api\Data\EntrySearchResultInterfaceFactory;
use GS\GuestBook\Api\EntryRepositoryInterface;
use GS\GuestBook\Model\ResourceModel\Entry\Collection;
use GS\GuestBook\Model\ResourceModel\Entry\CollectionFactory as EntryCollectionFactory;
use GS\GuestBook\Model\ResourceModel\EntryResource;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class EntryRepository implements EntryRepositoryInterface
{
    private EntryResource $resourceModel;
    private EntryFactory $entryFactory;
    private EntryCollectionFactory $entryCollectionFactory;
    private EntrySearchResultInterfaceFactory $searchResultFactory;

    /**
     * Constructor
     *
     * @param EntryResource $resourceModel
     * @param EntryFactory $entryFactory
     * @param EntryCollectionFactory $entryCollectionFactory
     * @param EntrySearchResultInterfaceFactory $searchResultFactory
     */
    public function __construct(
        EntryResource $resourceModel,
        EntryFactory $entryFactory,
        EntryCollectionFactory $entryCollectionFactory,
        EntrySearchResultInterfaceFactory $searchResultFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->entryFactory = $entryFactory;
        $this->entryCollectionFactory = $entryCollectionFactory;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * Load guestbook entry by ID
     *
     * @param int $id
     * @return EntryInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): EntryInterface
    {
        $entry = $this->entryFactory->create();
        $this->resourceModel->load($entry, $id);
        if (!$entry->getId()) {
            throw new NoSuchEntityException(__('Unable to find guestbook entry with ID "%1"', $id));
        }

        return $entry;
    }

    /**
     * Save a guestbook entry
     *
     * @param EntryInterface $entry
     * @return EntryInterface
     * @throws AlreadyExistsException|LocalizedException
     */
    public function save(EntryInterface $entry): EntryInterface
    {
        $this->validate($entry);

        try {
            $this->resourceModel->save($entry);
            return $entry;
        } catch (AlreadyExistsException $e) {
            throw new LocalizedException(__('Guestbook entry already exists: %1', $e->getMessage()), null, 0, 400);
        } catch (Exception $e) {
            throw new LocalizedException(__('There was an error while saving the guestbook entry: %1', $e->getMessage()), null, 0, 500);
        }
    }

    /**
     * Validate a guestbook entry
     *
     * @param EntryInterface $entry
     * @return void
     * @throws LocalizedException
     */
    public function validate(EntryInterface $entry): void
    {
        $fields = [
            'first_name' => ['value' => $entry->getFirstName(), 'maxLength' => 30],
            'last_name'  => ['value' => $entry->getLastName(), 'maxLength' => 30],
            'email'      => ['value' => $entry->getEmail(), 'maxLength' => 123, 'validateEmail' => true],
        ];

        foreach ($fields as $fieldName => $data) {
            $value = $data['value'];
            $maxLength = $data['maxLength'];

            if (empty($value)) {
                throw new LocalizedException(__("$fieldName cannot be empty"));
            } elseif (strlen($value) > $maxLength) {
                throw new LocalizedException(__("$fieldName cannot be longer than characters %1", $maxLength));
            }

            if (!empty($data['validateEmail']) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new LocalizedException(__('Invalid email format'));
            }
        }
    }

    /**
     * Delete guestbook entry by ID
     *
     * @param int $id
     * @return void
     * @throws NoSuchEntityException|LocalizedException
     */
    public function delete(int $id): void
    {
        try {
            $this->resourceModel->delete($this->getById($id));
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('Unable to find guestbook entry with ID "%1"', $id));
        } catch (Exception $e) {
            throw new LocalizedException(__('There was an error while deleting the data: %1', $e->getMessage()));
        }
    }

    /**
     * Retrieve list of guestbook entries based on search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return EntrySearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): EntrySearchResultInterface
    {
        $collection = $this->entryCollectionFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);

        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * Add filters to collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param Collection $collection
     * @return void
     */
    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection): void
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Add sorting order to collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param Collection $collection
     * @return void
     */
    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection): void
    {
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * Add paging information to collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param Collection $collection
     * @return void
     */
    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection): void
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    /**
     * Build search result object from collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param Collection $collection
     * @return EntrySearchResultInterface
     */
    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, Collection $collection): EntrySearchResultInterface
    {
        $searchResults = $this->searchResultFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
