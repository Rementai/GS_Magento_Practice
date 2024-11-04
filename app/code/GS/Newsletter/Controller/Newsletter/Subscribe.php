<?php

declare(strict_types=1);

/**
 * GS Newsletter
 *
 * @copyright Copyright (c) 2024 Gate-Software Sp. z o.o. (www.gate-software.com). All rights reserved.
 * @author    Gate-Software Practice Team
 * @author    sebastian.mazgaj@gate-commerce.com
 *
 * @package   GS_Newsletter
 */

namespace GS\Newsletter\Controller\Newsletter;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\SalesRule\Model\RuleFactory;

class Subscribe extends Action
{
    private const SUCCESS_MESSAGE = 'Thank you for subscribing! Check your email for a discount coupon.';
    private const ERROR_SUBSCRIPTION_FAILED = 'Subscription failed.';
    private const ERROR_EMAIL_SEND_FAILED = 'Failed to send email.';
    private const EMAIL_HEADER = "Newsletter Discount for ";
    private const EMAIL_DESCRIPTION = "Newsletter subscription discount";

    private const EMAIL_PARAM = 'email';
    private const COUPON_CODE_KEY = 'couponCode';
    private const COUPON_TEMPLATE_IDENTIFIER = 'newsletter_coupon_template';
    private const COUPON_DISCOUNT_AMOUNT = 10;

    protected JsonFactory $resultJsonFactory;
    protected SubscriberFactory $subscriberFactory;
    protected RuleFactory $ruleFactory;
    protected TransportBuilder $transportBuilder;
    protected StateInterface $inlineTranslation;

    /**
     * Subscribe constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param SubscriberFactory $subscriberFactory
     * @param RuleFactory $ruleFactory
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        SubscriberFactory $subscriberFactory,
        RuleFactory $ruleFactory,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->ruleFactory = $ruleFactory;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
    }

    /**
     * Executes subscription action for newsletter popup
     *
     * @return Json
     */
    public function execute(): Json
    {
        $result = $this->resultJsonFactory->create();
        $email = $this->getRequest()->getParam(self::EMAIL_PARAM);

        try {
            $subscriber = $this->createSubscriber($email);

            if (!$subscriber) {
                return $this->prepareErrorResponse($result, self::ERROR_SUBSCRIPTION_FAILED);
            }

            $couponCode = $this->generateCoupon($email);

            $this->sendCouponEmail($email, $couponCode);

            return $this->prepareSuccessResponse($result, self::SUCCESS_MESSAGE);
        } catch (Exception $e) {
            return $this->prepareErrorResponse($result, $e->getMessage());
        }
    }

    /**
     * Add person to newsletter subscriber list
     *
     * @param string $email
     * @return Subscriber|null
     */
    protected function createSubscriber(string $email): ?Subscriber
    {
        $subscriber = $this->subscriberFactory->create();
        $subscriber->setEmail($email);
        $subscriber->setStatus(Subscriber::STATUS_SUBSCRIBED);
        $subscriber->save();

        return $subscriber->getId() ? $subscriber : null;
    }

    /**
     * Success message
     *
     * @param Json $result
     * @param string $message
     * @return Json
     */
    protected function prepareSuccessResponse(Json $result, string $message): Json
    {
        return $result->setData(['success' => true, 'message' => $message]);
    }

    /**
     * Error message
     *
     * @param Json $result
     * @param string $message
     * @return Json
     */
    protected function prepareErrorResponse(Json $result, string $message): Json
    {
        return $result->setData(['success' => false, 'message' => $message]);
    }

    /**
     * Generate coupon for newsletter subscriber
     *
     * @param string $email
     * @return string
     */
    protected function generateCoupon(string $email): string
    {
        $rule = $this->ruleFactory->create();
        $rule->setName(self::EMAIL_HEADER . $email)
            ->setDescription(self::EMAIL_DESCRIPTION)
            ->setFromDate(date('Y-m-d'))
            ->setToDate(date('Y-m-d', strtotime('+1 year')))
            ->setIsActive(1)
            ->setSimpleAction('by_percent')
            ->setDiscountAmount(self::COUPON_DISCOUNT_AMOUNT)
            ->setTimesUsed(0)
            ->setCouponType(2)
            ->setUsesPerCustomer(1)
            ->setCouponCode('NL' . strtoupper(substr(md5($email), 0, 6)));
        $rule->save();

        return $rule->getCouponCode();
    }

    /**
     * Sends email with coupon
     *
     * @param string $email
     * @param string $couponCode
     * @return void
     * @throws MailException
     */
    protected function sendCouponEmail(string $email, string $couponCode): void
    {
        $this->inlineTranslation->suspend();

        try {
            $postObject = new DataObject();
            $postObject->setData([
                self::EMAIL_PARAM => $email,
                self::COUPON_CODE_KEY => $couponCode
            ]);

            $transport = $this->transportBuilder
                ->setTemplateIdentifier(self::COUPON_TEMPLATE_IDENTIFIER)
                ->setTemplateOptions([
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ])
                ->setTemplateVars(['data' => $postObject])
                ->setFrom('general')
                ->addTo($email)
                ->getTransport();

            $transport->sendMessage();
        } catch (Exception $e) {
            $this->inlineTranslation->resume();
            throw new MailException(__(
                self::ERROR_EMAIL_SEND_FAILED
            ));
        }

        $this->inlineTranslation->resume();
    }
}
