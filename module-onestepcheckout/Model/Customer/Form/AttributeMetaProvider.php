<?php
namespace Aheadworks\OneStepCheckout\Model\Customer\Form;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\CustomerMetadataInterface;
use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Mapper;
use Aheadworks\OneStepCheckout\Model\Customer\Form\AttributeMeta\AvailabilityChecker;
use Aheadworks\OneStepCheckout\Model\Customer\Form\Customization\Modifier as CustomizationModifier;

/**
 * Provide attribute metadata
 */
class AttributeMetaProvider
{
    /**
     * @var CustomerMetadataInterface
     */
    private $customerMetadata;

    /**
     * @var AvailabilityChecker
     */
    private $availabilityChecker;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var CustomizationModifier
     */
    private $customizationModifier;

    /**
     * @param CustomerMetadataInterface $customerMetadata
     * @param AvailabilityChecker $availabilityChecker
     * @param Mapper $mapper
     * @param CustomizationModifier $customizationModifier
     */
    public function __construct(
        CustomerMetadataInterface $customerMetadata,
        AvailabilityChecker $availabilityChecker,
        Mapper $mapper,
        CustomizationModifier $customizationModifier
    ) {
        $this->customerMetadata = $customerMetadata;
        $this->availabilityChecker = $availabilityChecker;
        $this->mapper = $mapper;
        $this->customizationModifier = $customizationModifier;
    }

    /**
     * Get customer attributes metadata
     *
     * @return array
     * @throws LocalizedException
     */
    public function getMetadata()
    {
        $result = [];
        $attributes = $this->customerMetadata->getAttributes('customer_account_create');
        foreach ($attributes as $attributeMeta) {
            $attributeMeta = clone $attributeMeta;
            if ($this->availabilityChecker->isAvailableOnForm($attributeMeta)) {
                $attributeCode = $attributeMeta->getAttributeCode();
                $attributeMeta = $this->customizationModifier->modify($attributeCode, $attributeMeta);
                $metadata = $this->mapper->map($attributeMeta);
                $metadata['backendType'] = $attributeMeta->getBackendType();

                if (isset($metadata['label'])) {
                    $metadata['label'] = __($metadata['label']);
                }

                $result[$attributeCode] = $metadata;
            }
        }

        return $result;
    }
}
