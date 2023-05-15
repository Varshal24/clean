<?php
namespace Aheadworks\OneStepCheckout\Model;

use Aheadworks\OneStepCheckout\Api\CartItemOptionsManagementInterface;
use Aheadworks\OneStepCheckout\Api\Data\CartItemOptionsDetailsInterface;
use Aheadworks\OneStepCheckout\Api\Data\CartItemOptionsDetailsInterfaceFactory;
use Aheadworks\OneStepCheckout\Model\Cart\ImageProvider;
use Aheadworks\OneStepCheckout\Model\Cart\OptionsProvider as ItemOptionsProvider;
use Aheadworks\OneStepCheckout\Model\Cart\Validator;
use Aheadworks\OneStepCheckout\Model\Product\ConfigurationPool;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

/**
 * Class CartItemOptionsManagement
 * @package Aheadworks\OneStepCheckout\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartItemOptionsManagement implements CartItemOptionsManagementInterface
{
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
     * @var CartItemOptionsProcessor
     */
    private $itemOptionsProcessor;

    /**
     * @var ConfigurationPool
     */
    private $configurationPool;

    /**
     * @var ImageProvider
     */
    private $imageProvider;

    /**
     * @var ItemOptionsProvider
     */
    private $itemOptionsProvider;

    /**
     * @var CartItemOptionsDetailsInterfaceFactory
     */
    private $optionDetailsFactory;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param Validator $quoteValidator
     * @param CartItemOptionsProcessor $itemOptionsProcessor
     * @param ConfigurationPool $configurationPool
     * @param ImageProvider $imageProvider
     * @param ItemOptionsProvider $itemOptionsProvider
     * @param PaymentInformationManagementInterface $paymentInformationManagement
     * @param CartItemOptionsDetailsInterfaceFactory $optionDetailsFactory
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Validator $quoteValidator,
        CartItemOptionsProcessor $itemOptionsProcessor,
        ConfigurationPool $configurationPool,
        ImageProvider $imageProvider,
        ItemOptionsProvider $itemOptionsProvider,
        PaymentInformationManagementInterface $paymentInformationManagement,
        CartItemOptionsDetailsInterfaceFactory $optionDetailsFactory,
        JsonSerializer $jsonSerializer
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteValidator = $quoteValidator;
        $this->itemOptionsProcessor = $itemOptionsProcessor;
        $this->configurationPool = $configurationPool;
        $this->imageProvider = $imageProvider;
        $this->itemOptionsProvider = $itemOptionsProvider;
        $this->paymentInformationManagement = $paymentInformationManagement;
        $this->optionDetailsFactory = $optionDetailsFactory;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function update($itemId, $cartId, $options)
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(
                __('Cart item %1 doesn\'t exist.', $itemId)
            );
        }

        $productType = $quoteItem->getProduct()->getTypeId();
        $quoteItem = $this->itemOptionsProcessor->addProductOptions($productType, $quoteItem);
        $quoteItem = $this->itemOptionsProcessor->applyCustomOptions($quoteItem);
        $this->configurationPool
            ->getConfiguration($productType)
            ->setOptions(
                $quoteItem,
                $this->jsonSerializer->unserialize($options)
            )
        ;

        $buyRequest = $this->itemOptionsProcessor->getBuyRequest($productType, $quoteItem);
        $quote->updateItem($itemId, $buyRequest);
        $this->quoteValidator->validate($quote);
        $this->quoteRepository->save($quote);

        /** @var CartItemOptionsDetailsInterface $optionDetails */
        $optionDetails = $this->optionDetailsFactory->create();
        $optionDetails
            ->setOptionsDetails(
                $this->jsonSerializer->serialize(
                    $this->itemOptionsProvider->getOptionsData($cartId)
                )
            )->setImageDetails(
                $this->jsonSerializer->serialize(
                    $this->imageProvider->getItemsImageData($cartId)
                )
            )->setPaymentDetails(
                $this->paymentInformationManagement->getPaymentInformation($cartId)
            );

        return $optionDetails;
    }
}
