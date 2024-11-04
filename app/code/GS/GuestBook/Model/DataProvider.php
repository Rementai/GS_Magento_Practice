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

use GS\GuestBook\Model\ResourceModel\Entry\CollectionFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var AbstractCollection
     */
    protected $collection;

    /**
     * DataProvider constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param string[]|mixed[] $meta
     * @param string[]|mixed[] $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    /**
     * Gets data from the collection.
     *
     * @return string[]|mixed[]
     */
    public function getData(): array
    {
        /** @var Entry[] $items */
        $items = $this->collection->getItems();
        if (empty($items)) {
            return [];
        }

        /** @var Entry $entry */
        $this->loadedData = [];
        foreach ($items as $entry) {
            $this->loadedData[$entry->getId()] = $entry->getData();
        }

        return $this->loadedData;
    }
}
