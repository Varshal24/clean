<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface GiftMessageItemManagementInterface
 * @package Aheadworks\OneStepCheckout\Api
 * @api
 */
interface GiftMessageItemManagementInterface
{
    /**
     * Set the gift message for a specified item in a specified shopping cart.
     *
     * @param int $cartId The cart ID.
     * @param \Magento\GiftMessage\Api\Data\MessageInterface $giftMessage The gift message.
     * @param int $itemId The item ID.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\InputException You cannot add gift messages to empty carts.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified gift message could not be saved.
     */
    public function update(int $cartId, \Magento\GiftMessage\Api\Data\MessageInterface $giftMessage, int $itemId): bool;
}
