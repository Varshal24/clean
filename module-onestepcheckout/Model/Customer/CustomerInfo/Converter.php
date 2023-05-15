<?php
namespace Aheadworks\OneStepCheckout\Model\Customer\CustomerInfo;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;

/**
 * Convert customer info data
 */
class Converter
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param SerializerInterface $serializer
     * @param CustomerInterfaceFactory $customerFactory
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        SerializerInterface $serializer,
        CustomerInterfaceFactory $customerFactory
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->serializer = $serializer;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Convert customer info to array
     *
     * @param CustomerInterface $customerInfo
     * @return array
     */
    public function toArray($customerInfo)
    {
        $customerInfoArray = $this->dataObjectProcessor->buildOutputDataArray(
            $customerInfo,
            CustomerInterface::class
        );

        return array_filter($customerInfoArray);
    }

    /**
     * Serialize customer info object
     *
     * @param CustomerInterface $customerInfo
     * @return bool|string
     */
    public function toSerializedString($customerInfo)
    {
        return $this->serializer->serialize($this->toArray($customerInfo));
    }

    /**
     * Convert serialized string to customer info object
     *
     * @param string $customerInfoSerialized
     * @return CustomerInterface
     */
    public function toObjectFromSerializedString($customerInfoSerialized)
    {
        $customerInfo = $this->customerFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $customerInfo,
            $this->toArrayFromSerializedString($customerInfoSerialized),
            CustomerInterface::class
        );

        return $customerInfo;
    }

    /**
     * Convert serialized string to customer info array
     *
     * @param string $customerInfoSerialized
     * @return array
     */
    public function toArrayFromSerializedString($customerInfoSerialized)
    {
        return $this->serializer->unserialize($customerInfoSerialized);
    }
}
