<?php
namespace Training\Sales\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;

class SessionInfo extends Action
{
    protected CustomerSession $customerSession;
    protected CheckoutSession $checkoutSession;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession
    ) {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    public function execute(): void
    {
        $customerData = $this->customerSession->getCustomer()->getData();
        $quote = $this->checkoutSession->getQuote();
        $quoteItems = $quote->getAllItems();

        $result = [
            'customer' => $customerData,
            'quote_items' => []
        ];

        foreach ($quoteItems as $item) {
            $result['quote_items'][] = $item->getData();
        }

        $this->getResponse()->setBody(json_encode($result));
    }
}
