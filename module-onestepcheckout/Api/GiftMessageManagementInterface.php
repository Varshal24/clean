<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface GiftMessageManagementInterface
 * @package Aheadworks\OneStepCheckout\Api
 * @api
 */
interface GiftMessageManagementInterface
{
    /**
     * Set the gift message for an entire order.
     *
     * @param int $cartId The cart ID.
     * @param \Magento\GiftMessage\Api\Data\MessageInterface $giftMessage The gift message.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\InputException You cannot add gift messages to empty carts.
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException You cannot add gift messages to
     * virtual products.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified gift message could not be saved.
     */
    public function update(int $cartId, \Magento\GiftMessage\Api\Data\MessageInterface $giftMessage): bool;
}
