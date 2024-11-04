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

use Magento\Framework\Api\SearchResults;
use GS\GuestBook\Api\Data\EntrySearchResultInterface;

class EntrySearchResult extends SearchResults implements EntrySearchResultInterface
{
}
