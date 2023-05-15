<?php
namespace Aheadworks\OneStepCheckout\Model\Checkout;

use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Process payment data
 */
interface PaymentDataProcessorInterface
{
    /**
     * Process payment data
     *
     * @param PaymentInterface $paymentData
     * @param int $cartId
     */
    public function process(PaymentInterface $paymentData, $cartId);
}
