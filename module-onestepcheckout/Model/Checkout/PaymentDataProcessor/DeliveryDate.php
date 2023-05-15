<?php
namespace Aheadworks\OneStepCheckout\Model\Checkout\PaymentDataProcessor;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Aheadworks\OneStepCheckout\Model\Cart\DeliveryDateAssigner;
use Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate\DisplayOption as DeliveryDateDisplayOption;
use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\Checkout\PaymentDataProcessorInterface;

/**
 * Process delivery date from payment data
 */
class DeliveryDate implements PaymentDataProcessorInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var DeliveryDateAssigner
     */
    private $deliveryDateAssigner;

    /**
     * @param Config $config
     * @param CartRepositoryInterface $quoteRepository
     * @param DeliveryDateAssigner $deliveryDateAssigner
     */
    public function __construct(
        Config $config,
        CartRepositoryInterface $quoteRepository,
        DeliveryDateAssigner $deliveryDateAssigner
    ) {
        $this->config = $config;
        $this->quoteRepository = $quoteRepository;
        $this->deliveryDateAssigner = $deliveryDateAssigner;
    }

    /**
     * @inheritdoc
     */
    public function process(PaymentInterface $paymentData, $cartId)
    {
        if ($this->config->getDeliveryDateDisplayOption() != DeliveryDateDisplayOption::NO) {
            $deliveryDate = $paymentData->getExtensionAttributes() === null
                ? false
                : $paymentData->getExtensionAttributes()->getDeliveryDate();
            $deliveryTimeSlot = $paymentData->getExtensionAttributes() === null
                ? false
                : $paymentData->getExtensionAttributes()->getDeliveryTimeSlot();

            if ($deliveryDate) {
                $quote = $this->quoteRepository->getActive($cartId);
                $this->deliveryDateAssigner->assign($quote, $deliveryDate, $deliveryTimeSlot);
            }
        }
    }
}
