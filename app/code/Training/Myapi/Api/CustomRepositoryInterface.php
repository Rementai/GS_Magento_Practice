<?php

namespace Training\Myapi\Api;

use Training\Myapi\Api\Data\CustomInterface;

interface CustomRepositoryInterface
{
    /**
     * Get list of items
     *
     * @return CustomInterface[]
     */
    public function getList();

    /**
     * Update item
     *
     * @param CustomInterface $item
     * @return CustomInterface
     */
    public function updateItem(CustomInterface $item);

    /**
     * Create item
     *
     * @param CustomInterface $item
     * @return CustomInterface
     */
    public function createItem(CustomInterface $item);

    /**
     * Delete item by ID
     *
     * @param int $itemId
     * @return bool
     */
    public function deleteItem(int $itemId);

    /**
     * Get item by ID
     *
     * @param int $itemId
     * @return CustomInterface
     */
    public function getById(int $itemId): CustomInterface;
}
