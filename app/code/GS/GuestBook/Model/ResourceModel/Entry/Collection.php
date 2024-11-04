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

namespace GS\GuestBook\Model\ResourceModel\Entry;

use GS\GuestBook\Model\Entry;
use GS\GuestBook\Model\ResourceModel\EntryResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialize the model and resource model for the collection.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Entry::class, EntryResource::class);
    }
}
