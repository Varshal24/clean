<?php
namespace Aheadworks\OneStepCheckout\Model\Attribute\Form\AttributeMeta;

use Magento\Customer\Api\Data\AttributeMetadataInterface;

/**
 * Class AvailabilityChecker
 */
abstract class AvailabilityChecker
{
    /**#@+
     * None supported attribute input types
     */
    const FILE = 'file';
    const IMAGE = 'image';
    /**#@-*/

    /**
     * Check if attribute is available on checkout form
     *
     * @param AttributeMetadataInterface $attributeMeta
     * @return bool
     */
    abstract public function isAvailableOnForm(AttributeMetadataInterface $attributeMeta);

    /**
     * Get non supported input types
     *
     * @return array
     */
    public function getNonSupportedInputTypes()
    {
        return [
            self::FILE,
            self::IMAGE
        ];
    }
}
