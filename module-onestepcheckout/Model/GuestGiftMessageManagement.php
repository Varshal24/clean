<?php
namespace Aheadworks\OneStepCheckout\Model;

use Magento\GiftMessage\Api\Data\MessageInterface;
use Aheadworks\OneStepCheckout\Api\GuestGiftMessageManagementInterface;
use Aheadworks\OneStepCheckout\Api\GiftMessageManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Class GuestGiftMessageManagement
 * @package Aheadworks\OneStepCheckout\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GuestGiftMessageManagement implements GuestGiftMessageManagementInterface
{
    /**
     * @var GiftMessageManagementInterface
     */
    private $giftMessageManagement;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @param GiftMessageManagementInterface $giftMessageManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        GiftMessageManagementInterface $giftMessageManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->giftMessageManagement = $giftMessageManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $cartId, MessageInterface $giftMessage): bool
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->giftMessageManagement->update((int)$quoteIdMask->getQuoteId(), $giftMessage);
    }
}
