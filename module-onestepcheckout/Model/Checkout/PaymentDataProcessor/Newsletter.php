<?php
namespace Aheadworks\OneStepCheckout\Model\Checkout\PaymentDataProcessor;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Quote\Api\Data\PaymentInterface;
use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\Checkout\PaymentDataProcessorInterface;

/**
 * Process newsletter from payment data
 */
class Newsletter implements PaymentDataProcessorInterface
{
    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var bool
     */
    private $isSubscribedFlag = false;

    /**
     * @param SubscriberFactory $subscriberFactory
     * @param Config $config
     * @param CustomerSession $customerSession
     */
    public function __construct(
        SubscriberFactory $subscriberFactory,
        Config $config,
        CustomerSession $customerSession
    ) {
        $this->subscriberFactory = $subscriberFactory;
        $this->config = $config;
        $this->customerSession = $customerSession;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function process(PaymentInterface $paymentData, $cartId)
    {
        if ($this->config->isNewsletterSubscribeOptionEnabled() && !$this->isSubscribedFlag) {
            $isSubscribeFlag = $paymentData->getExtensionAttributes() === null
                ? false
                : $paymentData->getExtensionAttributes()->getIsSubscribeForNewsletter();

            if ($isSubscribeFlag) {
                /** @var Subscriber $subscriber */
                $subscriber = $this->subscriberFactory->create();
                if ($this->customerSession->isLoggedIn()) {
                    $customerId = $this->customerSession->getCustomerId();
                    if (!$this->isSubscribedByCustomerId($customerId)) {
                        $subscriber->subscribeCustomerById($customerId);
                        $this->isSubscribedFlag = true;
                    }
                } else {
                    $email = $paymentData->getExtensionAttributes() === null
                        ? false
                        : $paymentData->getExtensionAttributes()->getSubscriberEmail();
                    if ($email && !$this->isSubscribedByEmail($email)) {
                        $subscriber->subscribe($email);
                        $this->isSubscribedFlag = true;
                    }
                }
            }
        }
    }

    /**
     * Check if subscribed by customer ID
     *
     * @param int $customerId
     * @return bool
     */
    private function isSubscribedByCustomerId($customerId)
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        return $subscriber
            ->loadByCustomerId($customerId)
            ->isSubscribed();
    }

    /**
     * Check if subscribed by email
     *
     * @param string $email
     * @return bool
     */
    private function isSubscribedByEmail($email)
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        return $subscriber
            ->loadByEmail($email)
            ->isSubscribed();
    }
}
