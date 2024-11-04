<?php

declare(strict_types=1);

/**
 * GS GuestBook
 *
 * @copyright Copyright (c) 2024 Gate-Software Sp. z o.o. All rights reserved.
 * @author    Gate-Software Practice Team
 * @author    sebastian.mazgaj@gate-commerce.com
 *
 * @package   GS_GuestBook
 */

namespace GS\GuestBook\Controller\Index;

use Exception;
use GS\GuestBook\Model\EntryFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Post extends Action
{
    private const SERVER_REMOTE_ADDR = 'REMOTE_ADDR';
    private const FIRST_NAME_ATTRIBUTE = 'first_name';
    private const LAST_NAME_ATTRIBUTE = 'last_name';
    private const EMAIL_ATTRIBUTE = 'email';
    private const IP_ATTRIBUTE = 'ip_address';
    private const VALIDATION_RESULT_KEY = 'is_valid';
    private const VALIDATION_MESSAGE_KEY = 'message';

    protected EntryFactory $entryFactory;

    /**
     * Controller index Post constructor
     *
     * @param Context $context
     * @param EntryFactory $entryFactory
     */
    public function __construct(
        Context $context,
        EntryFactory $entryFactory
    ) {
        parent::__construct($context);
        $this->entryFactory = $entryFactory;
    }

    /**
     * Execute action to handle form submission
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $postData = $this->getRequest()->getPostValue();

        $validationResult = $this->validateData($postData);
        if (!$validationResult[self::VALIDATION_RESULT_KEY]) {
            $this->messageManager->addErrorMessage(__($validationResult[self::VALIDATION_MESSAGE_KEY]));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($this->_redirect->getRefererUrl());
        }

        try {
            $this->processFormData($postData);
            $this->messageManager->addSuccessMessage(__('Thank you for submitting the form.'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while processing the form. Please try again.'));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($this->_redirect->getRefererUrl());
    }

    /**
     * Validate form data.
     *
     * @param string[] $postData
     * @return array
     */
    protected function validateData(array $postData): array
    {
        if (empty($postData['consent'])) {
            return [self::VALIDATION_RESULT_KEY => false, self::VALIDATION_MESSAGE_KEY => 'You must indicate your consent to the processing of personal data.'];
        }

        if (empty($postData[self::FIRST_NAME_ATTRIBUTE])) {
            return [self::VALIDATION_RESULT_KEY => false, self::VALIDATION_MESSAGE_KEY => 'First name is required.'];
        }

        if (empty($postData[self::LAST_NAME_ATTRIBUTE])) {
            return [self::VALIDATION_RESULT_KEY => false, self::VALIDATION_MESSAGE_KEY => 'Last name is required.'];
        }

        if (empty($postData[self::EMAIL_ATTRIBUTE]) || !filter_var($postData[self::EMAIL_ATTRIBUTE], FILTER_VALIDATE_EMAIL)) {
            return [self::VALIDATION_RESULT_KEY => false, self::VALIDATION_MESSAGE_KEY => 'A valid email address is required.'];
        }

        return [self::VALIDATION_RESULT_KEY => true, self::VALIDATION_MESSAGE_KEY => ''];
    }

    /**
     * Process form data and save it.
     *
     * @param string[] $postData
     * @return void
     */
    protected function processFormData(array $postData): void
    {
        $this->saveData(
            $postData[self::FIRST_NAME_ATTRIBUTE] ?? '',
            $postData[self::LAST_NAME_ATTRIBUTE] ?? '',
            $postData[self::EMAIL_ATTRIBUTE] ?? '',
            $this->getRequest()->getServer(self::SERVER_REMOTE_ADDR)
        );
    }

    /**
     * Save data to the database.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $ipAddress
     * @return void
     */
    protected function saveData(string $firstName, string $lastName, string $email, string $ipAddress): void
    {
        try {
            $entry = $this->entryFactory->create();
            $entry->setData([
                self::FIRST_NAME_ATTRIBUTE => $firstName,
                self::LAST_NAME_ATTRIBUTE => $lastName,
                self::EMAIL_ATTRIBUTE => $email,
                self::IP_ATTRIBUTE => $ipAddress
            ]);

            $entry->save();
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('There was an error while saving the data. Please try again.'));
        }
    }
}
