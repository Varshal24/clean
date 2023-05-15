<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface GuestItemRepositoryInterface
 * @package Aheadworks\OneStepCheckout\Api
 * @api
 */
interface GuestGiftMessageItemManagementInterface
{
    /**
     * Set the gift message for a specified item in a specified shopping cart.
     *
     * @param string $cartId The cart ID.
     * @param \Magento\GiftMessage\Api\Data\MessageInterface $giftMessage The gift message.
     * @param int $itemId The item ID.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\InputException You cannot add gift messages to empty carts.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified gift message could not be saved.
     */
    public function update(string $cartId, \Magento\GiftMessage\Api\Data\MessageInterface $giftMessage, int $itemId): bool;
}
