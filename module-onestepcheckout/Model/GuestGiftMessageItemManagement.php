<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model;

use Aheadworks\OneStepCheckout\Api\GiftMessageItemManagementInterface;
use Magento\GiftMessage\Api\Data\MessageInterface;
use Aheadworks\OneStepCheckout\Api\GuestGiftMessageItemManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Class GuestGiftMessageItemManagement
 * @package Aheadworks\OneStepCheckout\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GuestGiftMessageItemManagement implements GuestGiftMessageItemManagementInterface
{
    /**
     * @var GiftMessageItemManagementInterface
     */
    private $giftMessageItemManagement;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @param GiftMessageItemManagementInterface $giftMessageItemManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        GiftMessageItemManagementInterface $giftMessageItemManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->giftMessageItemManagement = $giftMessageItemManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $cartId, MessageInterface $giftMessage, int $itemId): bool
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->giftMessageItemManagement->update((int)$quoteIdMask->getQuoteId(), $giftMessage, $itemId);
    }
}
