<?php
namespace Aheadworks\OneStepCheckout\Model\Customer\Form\Customization;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Aheadworks\OneStepCheckout\Model\Address\Attribute\Code\Resolver as AddressAttributeCodeResolver;
use Aheadworks\OneStepCheckout\Model\Config as ModuleConfig;

/**
 * Modifies customer attributes meta data according to customization settings
 */
class Modifier
{
    /**
     * @var ModuleConfig
     */
    private $config;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var AddressAttributeCodeResolver
     */
    private $addressAttributeCodeResolver;

    /**
     * @param ModuleConfig $config
     * @param DataObjectHelper $dataObjectHelper
     * @param AddressAttributeCodeResolver $addressAttributeCodeResolver
     */
    public function __construct(
        ModuleConfig $config,
        DataObjectHelper $dataObjectHelper,
        AddressAttributeCodeResolver $addressAttributeCodeResolver
    ) {
        $this->config = $config;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->addressAttributeCodeResolver = $addressAttributeCodeResolver;
    }

    /**
     * Modify attribute metadata
     *
     * @param string $attributeCode
     * @param AttributeMetadataInterface $metadata
     * @return AttributeMetadataInterface
     */
    public function modify($attributeCode, $metadata)
    {
        $formConfig = $this->config->getCustomerInfoFormConfig();
        $metadataUpdate = $this->getMetadataFromConfig($attributeCode, $formConfig);
        if (!empty($metadataUpdate)) {
            $this->dataObjectHelper->populateWithArray(
                $metadata,
                [
                    AttributeMetadataInterface::STORE_LABEL => $metadataUpdate['label'],
                    AttributeMetadataInterface::VISIBLE => $metadataUpdate['visible'],
                    AttributeMetadataInterface::REQUIRED => $metadataUpdate['required']
                ],
                AttributeMetadataInterface::class
            );
        }
        return $metadata;
    }

    /**
     * Retrieve config metadata for specific attribute
     *
     * @param string $attributeCode
     * @param array $formConfig
     * @return array
     */
    private function getMetadataFromConfig($attributeCode, $formConfig)
    {
        $attributeMetadata = [];
        $attributesFormConfig = isset($formConfig['attributes']) ? $formConfig['attributes'] : [];
        $duplicatedAttributeCode = $this->addressAttributeCodeResolver->getDuplicatedAttributeCode($attributeCode);

        if (isset($attributesFormConfig[$attributeCode])) {
            $attributeMetadata = $attributesFormConfig[$attributeCode];
        } elseif (isset($attributesFormConfig[$duplicatedAttributeCode])) {
            $attributeMetadata = $attributesFormConfig[$duplicatedAttributeCode];
        }

        return $attributeMetadata;
    }
}
