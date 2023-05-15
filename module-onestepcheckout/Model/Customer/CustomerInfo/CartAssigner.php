<?php
namespace Aheadworks\OneStepCheckout\Model\Customer\CustomerInfo;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Assign customer info data to cart
 */
class CartAssigner
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Converter
     */
    private $customerInfoConverter;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param Converter $customerInfoConverter
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Converter $customerInfoConverter
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->customerInfoConverter = $customerInfoConverter;
    }

    /**
     * Assign customer info
     *
     * @param CustomerInterface $customerInfo
     * @param int $cartId
     * @throws NoSuchEntityException
     */
    public function assign($customerInfo, $cartId)
    {
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setAwOscCustomerInfo($this->customerInfoConverter->toSerializedString($customerInfo));
        $customerAttributes = $customerInfo->getCustomAttributes();
        foreach ($customerAttributes as $attribute) {
            $quote->setData('customer_' . $attribute->getAttributeCode(), $attribute->getValue());
        }
        $this->quoteRepository->save($quote);
    }
}
