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

namespace GS\GuestBook\Model\ResourceModel\Entry\Grid;

use GS\GuestBook\Model\ResourceModel\Entry\Collection as EntryCollection;
use GS\GuestBook\Model\Entry;
use GS\GuestBook\Model\ResourceModel\EntryResource;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Psr\Log\LoggerInterface;

class Collection extends EntryCollection implements SearchResultInterface
{
    protected AggregationInterface $aggregations;

    /**
     * Constructor
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param string $mainTable
     * @param string $eventPrefix
     * @param string $eventObject
     * @param string $resourceModel
     * @param string $model
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface        $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface       $eventManager,
        string                 $mainTable = 'guestbook_entries',
        string                 $eventPrefix = 'guestbook_entry_grid_collection',
        string                 $eventObject = 'guestbook_entry_grid_collection',
        string                 $resourceModel = EntryResource::class,
        string                 $model = Document::class,
        AdapterInterface       $connection = null,
        AbstractDb             $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     *  Retrieve aggregations for the search results.
     *
     * @return AggregationInterface
     */
    public function getAggregations(): AggregationInterface
    {
        return $this->aggregations;
    }

    /**
     * Set aggregations for the search results.
     *
     * @param AggregationInterface $aggregations
     * @return void
     */
    public function setAggregations($aggregations): void
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Retrieve the search criteria used for filtering.
     *
     * @return SearchCriteriaInterface|null
     */
    public function getSearchCriteria(): ?SearchCriteriaInterface
    {
        return null;
    }

    /**
     * Set search criteria for filtering the results.
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null): self
    {
        return $this;
    }

    /**
     * Retrieve the total number of items matching the search criteria.
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->getSize();
    }

    /**
     * Set the total number of items matching the search criteria.
     *
     * @param $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount): self
    {
        return $this;
    }

    /**
     * Set the collection items.
     *
     * @param Entry[]|null $items
     * @return $this
     */
    public function setItems(array $items = null): self
    {
        return $this;
    }
}
