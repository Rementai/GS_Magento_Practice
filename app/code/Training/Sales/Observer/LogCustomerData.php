<?php

declare(strict_types=1);

namespace Training\Sales\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Training\Sales\Logger\Logger;

/**
 *
 */
class LogCustomerData implements ObserverInterface
{
    protected CustomerSession $customerSession;
    protected Logger $logger;

    /**
     * LogCustomerData constructor
     *
     * @param CustomerSession $customerSession
     * @param Logger $logger
     */
    public function __construct(CustomerSession $customerSession, Logger $logger)
    {
        $this->customerSession = $customerSession;
        $this->logger = $logger;
    }

    /**
     * Logs customer data
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if (!$this->customerSession->isLoggedIn()) {
            return;
        }

        $customer = $this->customerSession->getCustomer();
        $customerData = json_encode($customer->getData());
        $this->logger->log('Customer Data: ' . $customerData);
    }
}
