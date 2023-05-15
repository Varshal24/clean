<?php
namespace Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta;

use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\ModuleManager;
use Aheadworks\OneStepCheckout\Model\Attribute\Form\AttributeMeta\AvailabilityChecker as AttributeAvailabilityChecker;

/**
 * Class AvailabilityChecker
 */
class AvailabilityChecker extends AttributeAvailabilityChecker
{
    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @param ModuleManager $moduleManager
     */
    public function __construct(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritdoc
     */
    public function isAvailableOnForm(AttributeMetadataInterface $attributeMeta)
    {
        if ($this->moduleManager->isAheadworksCustomerAttributesEnabled()
            || $this->moduleManager->isMagentoCustomerAttributesEnabled()
        ) {
            if (in_array($attributeMeta->getFrontendInput(), $this->getNonSupportedInputTypes())) {
                return false;
            }
        }

        return $attributeMeta->isVisible();
    }
}
