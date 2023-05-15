<?php
namespace Aheadworks\OneStepCheckout\Model;

use Aheadworks\OneStepCheckout\Api\Data\CartItemUpdateDetailsInterface;
use Aheadworks\OneStepCheckout\Api\Data\CartItemUpdateDetailsInterfaceFactory;
use Aheadworks\OneStepCheckout\Api\CartItemManagementInterface;
use Aheadworks\OneStepCheckout\Model\Cart\Validator;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\TotalsItemInterface;
use Magento\Quote\Model\Quote;

/**
 * Class CartItemManagement
 * @package Aheadworks\OneStepCheckout\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartItemManagement implements CartItemManagementInterface
{
    /**
     * @var CartItemRepositoryInterface
     */
    private $itemRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Validator
     */
    private $quoteValidator;

    /**
     * @var PaymentInformationManagementInterface
     */
    private $paymentInformationManagement;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CartItemUpdateDetailsInterfaceFactory
     */
    private $itemUpdateDetailsFactory;

    /**
     * @param CartItemRepositoryInterface $itemRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param Validator $quoteValidator
     * @param PaymentInformationManagementInterface $paymentInformationManagement
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param CartItemUpdateDetailsInterfaceFactory $itemUpdateDetailsFactory
     */
    public function __construct(
        CartItemRepositoryInterface $itemRepository,
        CartRepositoryInterface $quoteRepository,
        Validator $quoteValidator,
        PaymentInformationManagementInterface $paymentInformationManagement,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        CartItemUpdateDetailsInterfaceFactory $itemUpdateDetailsFactory
    ) {
        $this->itemRepository = $itemRepository;
        $this->quoteRepository = $quoteRepository;
        $this->quoteValidator = $quoteValidator;
        $this->paymentInformationManagement = $paymentInformationManagement;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->itemUpdateDetailsFactory = $itemUpdateDetailsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($itemId, $cartId)
    {
        $this->itemRepository->deleteById($cartId, $itemId);
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $this->quoteValidator->validate($quote->collectTotals());
        return $this->getCartItemUpdateDetails($cartId);
    }

    /**
     * {@inheritdoc}
     */
    public function update(TotalsItemInterface $item, $cartId)
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $itemId = $item->getItemId();

        if (!$quote->getItemById($itemId)) {
            throw new NoSuchEntityException(
                __('Cart item %1 doesn\'t exist.', $itemId)
            );
        }

        $items = [];
        foreach ($quote->getAllItems() as $quoteItem) {
            if ($quoteItem->getItemId() == $itemId) {
                $itemData = $this->dataObjectProcessor
                    ->buildOutputDataArray($item, TotalsItemInterface::class);
                $this->dataObjectHelper->populateWithArray($quoteItem, $itemData, TotalsItemInterface::class);
                $quoteItem->checkData();
            }
            $items[] = $quoteItem;
        }

        $quote->setItems($items);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $this->quoteValidator->validate($quote->collectTotals());
        $this->quoteRepository->save($quote);

        return $this->getCartItemUpdateDetails($cartId);
    }

    /**
     * Get item update details
     *
     * @param int $cartId
     * @return CartItemUpdateDetailsInterface
     */
    private function getCartItemUpdateDetails($cartId)
    {
        /** @var CartItemUpdateDetailsInterface $itemUpdateDetails */
        $itemUpdateDetails = $this->itemUpdateDetailsFactory->create();
        $quote = $this->quoteRepository->get($cartId);
        $paymentDetails = $this->paymentInformationManagement->getPaymentInformation($cartId);
        $itemUpdateDetails
            ->setCartDetails($quote)
            ->setPaymentDetails($paymentDetails);
        return $itemUpdateDetails;
    }
}
