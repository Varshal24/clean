<?php
namespace Aheadworks\OneStepCheckout\Model\Customer\Form\AttributeMeta;

use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\ModuleManager;
use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\AvailabilityChecker as AddressAttrAvailabilityChecker;

/**
 * Class AvailabilityChecker
 */
class AvailabilityChecker extends AddressAttrAvailabilityChecker
{
    const MULTILINE = 'multiline';

    /**
     * @var array
     */
    protected $restrictedAttributeCodes = [
        CustomerInterface::CREATED_AT,
        CustomerInterface::PREFIX,
        CustomerInterface::FIRSTNAME,
        CustomerInterface::MIDDLENAME,
        CustomerInterface::LASTNAME,
        CustomerInterface::SUFFIX,
        CustomerInterface::EMAIL
    ];

    /**
     * @param ModuleManager $moduleManager
     * @param array $restrictedAttributeCodes
     */
    public function __construct(
        ModuleManager $moduleManager,
        array $restrictedAttributeCodes = []
    ) {
        parent::__construct($moduleManager);
        $this->restrictedAttributeCodes = array_merge($restrictedAttributeCodes, $this->restrictedAttributeCodes);
    }

    /**
     * @inheritdoc
     */
    public function isAvailableOnForm(AttributeMetadataInterface $attributeMeta)
    {
        if (in_array($attributeMeta->getAttributeCode(), $this->restrictedAttributeCodes)) {
            return false;
        }

        return parent::isAvailableOnForm($attributeMeta);
    }

    /**
     * @inheritdoc
     */
    public function getNonSupportedInputTypes()
    {
        $nonSupportedTypes = parent::getNonSupportedInputTypes();
        $nonSupportedTypes[] = self::MULTILINE;

        return $nonSupportedTypes;
    }
}
