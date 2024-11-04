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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GuestBookEntry implements ResolverInterface
{
    private EntryRepositoryInterface $entryRepository;

    public function __construct(EntryRepositoryInterface $entryRepository)
    {
        $this->entryRepository = $entryRepository;
    }

    /**
     * Resolve method to get guestbook entry by ID
     *
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlInputException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {
        $id = $args['id'] ?? null;

        if ($id === null) {
            throw new GraphQlInputException(__('ID must be provided.'));
        }

        try {
            $entry = $this->entryRepository->getById((int)$id);

            return [
                'id' => $entry->getId(),
                'first_name' => $entry->getFirstName(),
                'last_name' => $entry->getLastName(),
                'email' => $entry->getEmail(),
                'ip_address' => $entry->getIpAddress(),
            ];
        } catch (NoSuchEntityException $e) {
            throw new GraphQlInputException(__('Guestbook entry with this ID does not exist', $id));
        } catch (Exception $e) {
            throw new GraphQlInputException(__('Unable to retrieve guestbook entry', $e->getMessage()));
        }
    }
}
