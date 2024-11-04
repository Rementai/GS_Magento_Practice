<?php

namespace Training\Myapi\Model;

use Training\Myapi\Api\CustomRepositoryInterface;
use Training\Myapi\Api\Data\CustomInterface;
use Training\Myapi\Api\Data\CustomInterfaceFactory;
use Training\Myapi\Model\ResourceModel\Custom\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class CustomRepository implements CustomRepositoryInterface
{
    protected $collectionFactory;
    protected $customFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        CustomInterfaceFactory $customFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->customFactory = $customFactory;
    }

    public function getList()
    {
        $collection = $this->collectionFactory->create();
        return $collection->getItems();
    }

    public function updateItem(CustomInterface $item)
    {
        $item->save();
        return $item;
    }

    public function createItem(CustomInterface $item)
    {
        $item->save();
        return $item;
    }

    public function deleteItem(int $itemId)
    {
        if (!$itemId) {
            throw new \InvalidArgumentException(__('Item ID is required'));
        }

        $item = $this->customFactory->create()->load($itemId);
        if (!$item->getId()) {
            throw new NoSuchEntityException(__('Item with that ID does not exist.', $itemId));
        }

        $item->delete();
        return true;
    }

    public function getById(int $itemId): CustomInterface
    {
        $item = $this->customFactory->create()->load($itemId);
        if (!$item->getId()) {
            throw new NoSuchEntityException(__('Item with that ID does not exist.', $itemId));
        }
        return $item;
    }
}
