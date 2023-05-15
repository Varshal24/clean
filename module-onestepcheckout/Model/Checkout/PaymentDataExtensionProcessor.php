<?php
namespace Aheadworks\OneStepCheckout\Model\Checkout;

use Magento\Quote\Api\Data\PaymentInterface;

class PaymentDataExtensionProcessor
{
    /**
     * @var PaymentDataProcessorInterface[]
     */
    private $processors;

    /**
     * @param PaymentDataProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Process payment data
     *
     * @param PaymentInterface $paymentData
     * @param int $cartId
     */
    public function process(PaymentInterface $paymentData, $cartId)
    {
        foreach ($this->processors as $processor) {
            if (!$processor instanceof PaymentDataProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf('Processor instance does not extend %s.', PaymentDataProcessorInterface::class)
                );
            }
            $processor->process($paymentData, $cartId);
        }
    }
}
