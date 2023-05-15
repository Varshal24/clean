<?php
namespace Aheadworks\OneStepCheckout\Model\Customer\Attribute;

use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Provides attribute value for customer
 */
class ValueProvider
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->customerRepository = $customerRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Get static attribute value for customer
     *
     * @param int $customerId
     * @param string $attributeCode
     * @return string|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getStaticAttributeValue($customerId, $attributeCode)
    {
        $customer = $this->customerRepository->getById($customerId);
        $customerData = $this->dataObjectProcessor->buildOutputDataArray(
            $customer,
            CustomerInterface::class
        );

        return $customerData[$attributeCode] ?? null;
    }

    /**
     * Get attribute value for customer
     *
     * @param int $customerId
     * @param string $attributeCode
     * @return AttributeInterface|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAttributeValue($customerId, $attributeCode)
    {
        $customer = $this->customerRepository->getById($customerId);
        $attribute = $customer->getCustomAttribute($attributeCode);

        return $attribute ? $attribute->getValue() : null;
    }
}
