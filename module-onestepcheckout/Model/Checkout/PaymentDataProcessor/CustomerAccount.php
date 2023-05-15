<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\Checkout\PaymentDataProcessor;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Api\Data\PaymentInterface;
use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\Checkout\PaymentDataProcessorInterface;
use Aheadworks\OneStepCheckout\Model\Data\Storage;

class CustomerAccount implements PaymentDataProcessorInterface
{
    /**
     * @param Storage $storage
     * @param Config $config
     * @param CustomerSession $customerSession
     */
    public function __construct(
        private Storage $storage,
        private Config $config,
        private CustomerSession $customerSession
    ) {
    }

    /**
     * Save intermediate values
     *
     * @param PaymentInterface $paymentData
     * @param string $cartId
     * @retrun void
     */
    public function process(PaymentInterface $paymentData, $cartId): void
    {
        if ($this->config->isAllowedCreateAccountAfterCheckout()
            && !$this->customerSession->isLoggedIn()
        ) {
            $extensionAttributes = $paymentData->getExtensionAttributes();
            $isShouldCreatedAccount = $extensionAttributes === null
                ? false
                : $extensionAttributes->getIsShouldCreatedAccount();

            $this->storage->add(Storage::IS_SHOULD_CREATED_ACCOUNT, $isShouldCreatedAccount);
        }
    }
}
