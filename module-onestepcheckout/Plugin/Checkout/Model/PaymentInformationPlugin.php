<?php
namespace Aheadworks\OneStepCheckout\Plugin\Checkout\Model;

use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Aheadworks\OneStepCheckout\Model\Checkout\PaymentDataExtensionProcessor;

/**
 * Plugin for \Magento\Checkout\Api\PaymentInformationManagementInterface
 */
class PaymentInformationPlugin
{
    /**
     * @var PaymentDataExtensionProcessor
     */
    private $paymentDataProcessor;

    /**
     * @param PaymentDataExtensionProcessor $paymentDataProcessor
     */
    public function __construct(
        PaymentDataExtensionProcessor $paymentDataProcessor
    ) {
        $this->paymentDataProcessor = $paymentDataProcessor;
    }
    /**
     * Execute payment data processors to process payment data extension attributes
     *
     * @param PaymentInformationManagementInterface $subject
     * @param int $cartId
     * @param PaymentInterface $paymentMethod
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        PaymentInformationManagementInterface $subject,
        $cartId,
        PaymentInterface $paymentMethod
    ) {
        $this->paymentDataProcessor->process($paymentMethod, $cartId);
    }

    /**
     * Execute payment data processors to process payment data extension attributes
     *
     * @param PaymentInformationManagementInterface $subject
     * @param int $cartId
     * @param PaymentInterface $paymentMethod
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSavePaymentInformation(
        PaymentInformationManagementInterface $subject,
        $cartId,
        PaymentInterface $paymentMethod
    ) {
        $this->paymentDataProcessor->process($paymentMethod, $cartId);
    }
}
