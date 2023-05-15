<?php
namespace Aheadworks\OneStepCheckout\Model\Checkout\PaymentDataProcessor;

use Magento\Quote\Api\Data\PaymentInterface;
use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\Customer\CustomerInfo\CartAssigner as CustomerInfoAssigner;
use Aheadworks\OneStepCheckout\Model\Checkout\PaymentDataProcessorInterface;

/**
 * Process customer info from payment data
 */
class CustomerInfo implements PaymentDataProcessorInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerInfoAssigner
     */
    private $customerInfoAssigner;

    /**
     * @param Config $config
     * @param CustomerInfoAssigner $customerInfoAssigner
     */
    public function __construct(
        Config $config,
        CustomerInfoAssigner $customerInfoAssigner
    ) {
        $this->config = $config;
        $this->customerInfoAssigner = $customerInfoAssigner;
    }

    /**
     * @inheritdoc
     */
    public function process(PaymentInterface $paymentData, $cartId)
    {
        if ($this->config->isCustomerInfoSectionDisplayed()) {
            $extensionAttributes = $paymentData->getExtensionAttributes();
            $customerInfo = $extensionAttributes === null
                ? null
                : $extensionAttributes->getAwOscCustomerInfo();

            if ($customerInfo) {
                $this->customerInfoAssigner->assign($customerInfo, $cartId);
            }
        }
    }
}
