<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\GiftMessage\Model\ItemRepository as GiftMessageItemRepository;
use Magento\GiftMessage\Api\Data\MessageInterface;
use Aheadworks\OneStepCheckout\Api\GiftMessageItemManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\OneStepCheckout\Model\Cart\Validator;

/**
 * Class GiftMessageItemManagement
 * @package Aheadworks\OneStepCheckout\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftMessageItemManagement implements GiftMessageItemManagementInterface
{
    /**
     * @var GiftMessageItemRepository
     */
    private $giftMessageItemRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Validator
     */
    private $quoteValidator;

    /**
     * @param GiftMessageItemRepository $giftMessageItemRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param Validator $quoteValidator
     */
    public function __construct(
        GiftMessageItemRepository $giftMessageItemRepository,
        CartRepositoryInterface $quoteRepository,
        Validator $quoteValidator
    ) {
        $this->giftMessageItemRepository = $giftMessageItemRepository;
        $this->quoteRepository = $quoteRepository;
        $this->quoteValidator = $quoteValidator;
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $cartId, MessageInterface $giftMessage, int $itemId): bool
    {
        $quote = $this->quoteRepository->getActive($cartId);

        if (!$quote->getItemById($itemId)) {
            throw new NoSuchEntityException(
                __('Cart item %1 doesn\'t exist.', $itemId)
            );
        }

        $this->giftMessageItemRepository->save($cartId, $giftMessage, $itemId);
        $this->quoteValidator->validate($quote->collectTotals());
        $this->quoteRepository->save($quote);

        return true;
    }
}
