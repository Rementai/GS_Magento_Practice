<?php

declare(strict_types=1);

/**
 * GS PhoneLogin
 *
 * @copyright Copyright (c) 2024 Gate-Software Sp. z o.o. (www.gate-software.com). All rights reserved.
 * @author    Gate-Software Practice Team
 * @author    sebastian.mazgaj@gate-commerce.com
 *
 * @package   GS_PhoneLogin
 */

namespace GS\PhoneLogin\Controller\Account;

use Exception;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Controller\Account\LoginPost as BaseLoginPost;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;

class LoginPost extends BaseLoginPost
{
    private const REDIRECT_PATH_DEFAULT = '*/*/';
    private const REDIRECT_PATH_SUCCESS = '';
    private const REDIRECT_DASHBOARD_CONFIG = 'customer/startup/redirect_dashboard';
    private const COOKIE_NAME_CACHE_SESSID = 'mage-cache-sessid';
    private const EMAIL_SEPARATOR = '@';

    private const FIELD_USERNAME = 'username';
    private const FIELD_PASSWORD = 'password';
    private const FIELD_PHONE_NUMBER = 'phone_number';
    private const FIELD_EMAIL = 'email';
    private const FIELD_LOGIN = 'login';

    /** @var AccountManagementInterface $customerAccountManagement */
    protected $customerAccountManagement;

    /** @var Validator $formKeyValidator */
    protected $formKeyValidator;

    /** @var AccountRedirect $accountRedirect */
    protected $accountRedirect;

    private Context $context;
    private Session $customerSession;
    private CustomerUrl $customerUrl;
    private CollectionFactory $customerFactory;
    private PhpCookieManager $cookieManager;
    private CookieMetadataFactory $cookieMetadataFactory;
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerUrl $customerUrl,
        Validator $formKeyValidator,
        AccountRedirect $accountRedirect,
        CollectionFactory $customerFactory,
        PhpCookieManager $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context, $customerSession, $customerAccountManagement, $customerUrl, $formKeyValidator, $accountRedirect);
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->scopeConfig = $scopeConfig;
        $this->customerUrl = $customerUrl;
        $this->_customerFactory = $customerFactory;
    }

    /**
     * Executes login form
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        if ($this->isInvalidSessionOrForm()) {
            return $this->getDefaultRedirect();
        }

        if (!$this->getRequest()->isPost()) {
            return $this->accountRedirect->getRedirect();
        }

        $login = $this->getLoginData();
        if ($this->isPhoneLogin($login[self::FIELD_USERNAME])) {
            $login[self::FIELD_USERNAME] = $this->getEmailFromPhoneNumber($login[self::FIELD_USERNAME]);
        }

        if (empty($login[self::FIELD_USERNAME]) || empty($login[self::FIELD_PASSWORD])) {
            $this->messageManager->addError(__('A login and a password are required.'));
            return $this->accountRedirect->getRedirect();
        }

        return $this->attemptLogin($login);
    }

    /**
     * Checks if session is valid and if login form is sent
     *
     * @return bool
     */
    private function isInvalidSessionOrForm(): bool
    {
        return $this->session->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest());
    }

    /**
     * Returns default Redirect instance
     *
     * @return Redirect
     */
    private function getDefaultRedirect(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath(self::REDIRECT_PATH_DEFAULT);

        return $resultRedirect;
    }

    /**
     * Fetches login info
     *
     * @return string[]
     */
    private function getLoginData(): array
    {
        return $this->getRequest()->getPost(self::FIELD_LOGIN);
    }

    /**
     * Checks if username logs in with phone number
     *
     * @param string $username
     * @return bool
     */
    private function isPhoneLogin(string $username): bool
    {
        return !str_contains($username, self::EMAIL_SEPARATOR);
    }

    /**
     * Returns email matching phone number
     *
     * @param string $phoneNumber
     * @return string|null
     */
    private function getEmailFromPhoneNumber(string $phoneNumber): ?string
    {
        $customerCollection = $this->_customerFactory->create();
        $customerCollection->addFieldToFilter(self::FIELD_PHONE_NUMBER, $phoneNumber);

        foreach ($customerCollection as $customerData) {
            return $customerData[self::FIELD_EMAIL];
        }

        return null;
    }

    /**
     * Handles user login
     *
     * @param string[] $login
     * @return Redirect
     */
    private function attemptLogin(array $login): Redirect
    {
        try {
            $customer = $this->customerAccountManagement->authenticate(
                $login[self::FIELD_USERNAME],
                $login[self::FIELD_PASSWORD]
            );
            $this->processSuccessfulLogin($customer);

            return $this->handlePostLoginRedirect();
        } catch (EmailNotConfirmedException $e) {
            $this->handleEmailNotConfirmed($login[self::FIELD_USERNAME]);
        } catch (UserLockedException $e) {
            $this->handleUserLocked();
        } catch (AuthenticationException $e) {
            $this->handleAuthenticationError();
        } catch (LocalizedException $e) {
            $this->handleLocalizedError($e);
        } catch (Exception $e) {
            $this->handleGeneralError();
        }

        return $this->accountRedirect->getRedirect();
    }

    /**
     * Sets up a session and processes successful login
     *
     * @param CustomerInterface $customer
     * @return void
     */
    private function processSuccessfulLogin(CustomerInterface $customer): void
    {
        $this->session->setCustomerDataAsLoggedIn($customer);
        $this->session->regenerateId();
        $this->deleteCacheCookie();
    }

    /**
     * Deletes the mage-cache-sessid cookie
     *
     * @return void
     * @throws InputException
     * @throws FailureToSendException
     */
    private function deleteCacheCookie(): void
    {
        if (!$this->cookieManager->getCookie(self::COOKIE_NAME_CACHE_SESSID)) {
            return;
        }

        $metadata = $this->cookieMetadataFactory->createCookieMetadata();
        $metadata->setPath('/');
        $this->cookieManager->deleteCookie(self::COOKIE_NAME_CACHE_SESSID, $metadata);
    }

    /**
     * Handles redirection after login
     *
     * @return Redirect
     */
    private function handlePostLoginRedirect(): Redirect
    {
        $redirectUrl = $this->accountRedirect->getRedirectCookie();

        if ($this->scopeConfig->getValue(self::REDIRECT_DASHBOARD_CONFIG) && !$redirectUrl) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath(self::REDIRECT_PATH_SUCCESS);

            return $resultRedirect;
        }

        if ($redirectUrl) {
            $this->accountRedirect->clearRedirectCookie();
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($this->_redirect->success($redirectUrl));

            return $resultRedirect;
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath(self::REDIRECT_PATH_SUCCESS);

        return $resultRedirect;
    }

    /**
     * Handles exception, displays message
     *
     * @param string $username
     * @return void
     */
    private function handleEmailNotConfirmed(string $username): void
    {
        $message = __(
            'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
            $this->customerUrl->getEmailConfirmationUrl($username)
        );
        $this->messageManager->addError($message);
        $this->session->setUsername($username);
    }

    /**
     * Handles exception, displays message
     *
     * @return void
     */
    private function handleUserLocked(): void
    {
        $this->messageManager->addError(__('You did not sign in correctly or your account is temporarily disabled.'));
    }

    /**
     * Handles exception, displays message
     *
     * @return void
     */
    private function handleAuthenticationError(): void
    {
        $this->messageManager->addError(__('You did not sign in correctly or your account is temporarily disabled.'));
    }

    /**
     * Handles exception, displays message
     *
     * @param LocalizedException $e
     * @return void
     */
    private function handleLocalizedError(LocalizedException $e): void
    {
        $this->messageManager->addError($e->getMessage());
    }

    /**
     * Handles generic exception, displays message
     *
     * @return void
     */
    private function handleGeneralError(): void
    {
        $this->messageManager->addError(
            __('An unspecified error occurred. Please contact us for assistance.')
        );
    }
}
