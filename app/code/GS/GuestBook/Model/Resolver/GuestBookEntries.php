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

namespace GS\GuestBook\Model\Resolver;

use Exception;
use GS\GuestBook\Api\EntryRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilderResolver;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GuestBookEntries implements ResolverInterface
{
    private EntryRepositoryInterface $entryRepository;
    private SearchCriteriaBuilderResolver $searchCriteriaBuilderResolver;

    public function __construct(
        EntryRepositoryInterface $entryRepository,
        SearchCriteriaBuilderResolver $searchCriteriaBuilderResolver
    ) {
        $this->entryRepository = $entryRepository;
        $this->searchCriteriaBuilderResolver = $searchCriteriaBuilderResolver;
    }

    /**
     * Resolve guestbook entries list
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlInputException
     * @throws LocalizedException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {
        try {
            $searchCriteria = $this->searchCriteriaBuilderResolver->build('guestbook_entries', $args);

            $searchResult = $this->entryRepository->getList($searchCriteria);

            $entriesData = [];
            foreach ($searchResult->getItems() as $entry) {
                $entriesData[] = [
                    'id' => $entry->getId(),
                    'first_name' => $entry->getFirstName(),
                    'last_name' => $entry->getLastName(),
                    'email' => $entry->getEmail(),
                    'ip_address' => $entry->getIpAddress(),
                ];
            }

            return [
                'totalCount' => $searchResult->getTotalCount(),
                'items' => $entriesData,
            ];
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__('Error retrieving guestbook entries: %1', $e->getMessage()));
        } catch (Exception $e) {
            throw new GraphQlInputException(__('An error occurred while processing your request: %1', $e->getMessage()));
        }
    }
}
