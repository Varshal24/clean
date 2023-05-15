<?php
namespace Aheadworks\OneStepCheckout\Model\Checkout\PaymentDataProcessor;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\Checkout\PaymentDataProcessorInterface;

/**
 * Process order note from payment data
 */
class OrderNote implements PaymentDataProcessorInterface
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
     * @param Config $config
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        Config $config,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->config = $config;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @inheritdoc
     */
    public function process(PaymentInterface $paymentData, $cartId)
    {
        if ($this->config->isOrderNoteEnabled()) {
            $extensionAttributes = $paymentData->getExtensionAttributes();
            $orderNote = $extensionAttributes === null
                ? false
                : $extensionAttributes->getOrderNote();

            if ($orderNote) {
                $quote = $this->quoteRepository->getActive($cartId);
                $quote->setAwOrderNote($orderNote);
                $this->quoteRepository->save($quote);
            }
        }
    }
}
