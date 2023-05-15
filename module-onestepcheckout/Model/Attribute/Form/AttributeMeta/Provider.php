<?php
namespace Aheadworks\OneStepCheckout\Model\Attribute\Form\AttributeMeta;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Api\MetadataInterface;

/**
 * Provides attribute metadata
 */
class Provider
{
    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @param MetadataInterface $metadata
     */
    public function __construct(
        MetadataInterface $metadata
    ) {
        $this->metadata = $metadata;
    }

    /**
     * Get attribute metadata
     *
     * @param string $attributeCode
     * @return AttributeMetadataInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAttributeMetadata($attributeCode)
    {
        return $this->metadata->getAttributeMetadata($attributeCode);
    }

    /**
     * Get attribute metadata list
     *
     * @param string $formCode
     * @return AttributeMetadataInterface[]
     * @throws LocalizedException
     */
    public function getAttributeMetadataList($formCode)
    {
        return $this->metadata->getAttributes($formCode);
    }

    /**
     * Check if attribute is multiline
     *
     * @param string $attributeCode
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isMultiline($attributeCode)
    {
        return $this->getMultilineCount($attributeCode) > 1;
    }

    /**
     * Get multiline count
     *
     * @param string $attributeCode
     * @return int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getMultilineCount($attributeCode)
    {
        $metadata = $this->metadata->getAttributeMetadata($attributeCode);
        return $metadata->getMultilineCount();
    }
}
