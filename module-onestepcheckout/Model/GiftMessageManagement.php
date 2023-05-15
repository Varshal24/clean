<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model;

use Magento\GiftMessage\Api\Data\MessageInterface;
use Aheadworks\OneStepCheckout\Api\GiftMessageManagementInterface;
use Magento\GiftMessage\Model\CartRepository;
use Magento\Quote\Api\CartRepositoryInterface;
use Aheadworks\OneStepCheckout\Model\Cart\Validator;

/**
 * Class GiftMessageManagement
 * @package Aheadworks\OneStepCheckout\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftMessageManagement implements GiftMessageManagementInterface
{
    /**
     * @var CartRepository
     */
    private $cartRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Validator
     */
    private $quoteValidator;

    /**
     * @param CartRepository $cartRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param Validator $quoteValidator
     */
    public function __construct(
        CartRepository $cartRepository,
        CartRepositoryInterface $quoteRepository,
        Validator $quoteValidator
    ) {
        $this->cartRepository = $cartRepository;
        $this->quoteRepository = $quoteRepository;
        $this->quoteValidator = $quoteValidator;
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $cartId, MessageInterface $giftMessage): bool
    {
        $this->cartRepository->save($cartId, $giftMessage);
        $quote = $this->quoteRepository->getActive($cartId);
        $this->quoteValidator->validate($quote->collectTotals());
        $this->quoteRepository->save($quote);

        return true;
    }
}
