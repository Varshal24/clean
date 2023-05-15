<?php
namespace Aheadworks\OneStepCheckout\Model\Customer\Form\FieldRow;

/**
 * Field row mapper
 */
class Mapper
{
    /**
     * @var Configurator
     */
    private $configurator;

    /**
     * @param Configurator $configurator
     */
    public function __construct(
        Configurator $configurator
    ) {
        $this->configurator = $configurator;
    }

    /**
     * Map to custom field row
     *
     * @param string $attributeCode
     * @return string|null
     */
    public function toCustomFieldRow($attributeCode)
    {
        $customizationConfig = $this->configurator->getCustomizationConfig();
        return isset($customizationConfig['fields'][$attributeCode]['row_name'])
            ? $customizationConfig['fields'][$attributeCode]['row_name']
            : null;
    }
}
