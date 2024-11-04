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
use GS\GuestBook\Api\Data\EntryInterfaceFactory;
use GS\GuestBook\Api\EntryRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class AddGuestBookEntry implements ResolverInterface
{
    public function __construct(
        private EntryRepositoryInterface $entryRepository,
        private EntryInterfaceFactory $entryFactory
    ) {
    }

    /**
     * Resolve method to add guestbook entry.
     *
     * @param Field $field
     * @param mixed $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array<string, mixed>|null $args
     * @return array<string, mixed>
     * @throws GraphQlInputException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {
        $firstName = $args['first_name'] ?? null;
        $lastName = $args['last_name'] ?? null;
        $email = $args['email'] ?? null;
        $ipAddress = $args['ip_address'] ?? null;

        $this->validateInput($firstName, $lastName, $email);

        try {
            $entry = $this->entryFactory->create();
            $entry->setFirstName($firstName);
            $entry->setLastName($lastName);
            $entry->setEmail($email);
            $entry->setIpAddress($ipAddress);

            $this->entryRepository->save($entry);

            return $entry->getData();
        } catch (Exception $e) {
            throw new GraphQlInputException(__('Unable to add guestbook entry: %1', $e->getMessage()));
        }
    }

    /**
     * Validates the input arguments.
     *
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $email
     * @return void
     * @throws GraphQlInputException
     */
    private function validateInput(?string $firstName, ?string $lastName, ?string $email): void
    {
        if ($firstName === null) {
            throw new GraphQlInputException(__('First name is required'));
        }

        if ($lastName === null) {
            throw new GraphQlInputException(__('Last name is required'));
        }

        if ($email === null) {
            throw new GraphQlInputException(__('Email is required'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new GraphQlInputException(__('Invalid email format'));
        }
    }
}
