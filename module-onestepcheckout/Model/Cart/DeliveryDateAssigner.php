<?php
namespace Aheadworks\OneStepCheckout\Model\Cart;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\OneStepCheckout\Model\DateTime\Formatter as DateTimeFormatter;

/**
 * Class DeliveryDateAssigner
 *
 * @package Aheadworks\OneStepCheckout\Model\Cart
 */
class DeliveryDateAssigner
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var DateTimeFormatter
     */
    private $dateTimeFormatter;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param DateTimeFormatter $dateTimeFormatter
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        DateTimeFormatter $dateTimeFormatter
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    /**
     * Assign delivery date on cart
     *
     * @param CartInterface|Quote $quote
     * @param string $deliveryDate
     * @param string|null $deliveryTimeSlot
     * @throws \Exception
     */
    public function assign($quote, $deliveryDate, $deliveryTimeSlot)
    {
        $quote->setAwDeliveryDate($this->dateTimeFormatter->getLocalizedDeliveryDateTime($deliveryDate));

        if ($deliveryTimeSlot) {
            $fromTo = explode('-', $deliveryTimeSlot);
            $quote
                ->setAwDeliveryDateFrom(
                    $this->dateTimeFormatter->getLocalizedDeliveryDateTime($deliveryDate, $fromTo[0])
                )
                ->setAwDeliveryDateTo(
                    $this->dateTimeFormatter->getLocalizedDeliveryDateTime($deliveryDate, $fromTo[1])
                );
        }

        $this->quoteRepository->save($quote);
    }
}
