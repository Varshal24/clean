<?php
namespace Aheadworks\OneStepCheckout\Plugin\Checkout\Model;

use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Aheadworks\OneStepCheckout\Model\Checkout\PaymentDataExtensionProcessor;

/**
 * Plugin for \Magento\Checkout\Api\GuestPaymentInformationManagementInterface
 */
class GuestPaymentInformationPlugin
{
    /**
     * @var PaymentDataExtensionProcessor
     */
    private $paymentDataProcessor;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @param PaymentDataExtensionProcessor $paymentDataProcessor
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        PaymentDataExtensionProcessor $paymentDataProcessor,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->paymentDataProcessor = $paymentDataProcessor;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    /**
     * Execute payment data processors to process payment data extension attributes
     *
     * @param GuestPaymentInformationManagementInterface $subject
     * @param string $cartId
     * @param string $email
     * @param PaymentInterface $paymentMethod
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        GuestPaymentInformationManagementInterface $subject,
        $cartId,
        $email,
        PaymentInterface $paymentMethod
    ) {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()
            ->load($cartId, 'masked_id');
        $this->paymentDataProcessor->process($paymentMethod, $quoteIdMask->getQuoteId());
    }

    /**
     * Execute payment data processors to process payment data extension attributes
     *
     * @param GuestPaymentInformationManagementInterface $subject
     * @param string $cartId
     * @param string $email
     * @param PaymentInterface $paymentMethod
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSavePaymentInformation(
        GuestPaymentInformationManagementInterface $subject,
        $cartId,
        $email,
        PaymentInterface $paymentMethod
    ) {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()
            ->load($cartId, 'masked_id');
        $this->paymentDataProcessor->process($paymentMethod, $quoteIdMask->getQuoteId());
    }
}
