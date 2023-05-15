<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes;

/**
 * Provides different helper methods for ui component config
 */
class UiConfigHelper
{
    /**
     * @var array
     */
    private $initiallyHiddenAddressFields = ['region'];

    /**
     * Get new field row config
     *
     * @return array
     */
    public function getNewRowConfig()
    {
        return [
            'component' => 'uiComponent',
            'config' => [
                'template' => 'Aheadworks_OneStepCheckout/form/field-row'
            ],
            'children' => []
        ];
    }

    /**
     * Apply row template depending on element count
     *
     * @param array $row
     */
    public function resolveRowTemplate(&$row)
    {
        $row['config']['template'] = count($row) > 1
            ? 'Aheadworks_OneStepCheckout/form/field-row-fluid'
            : 'Aheadworks_OneStepCheckout/form/field-row';
    }

    /**
     * Check if attribute is visible on frontend
     *
     * @param array $attributeConfig
     * @param array $additionalConfig
     * @return bool
     */
    public function isFieldVisible(array $attributeConfig, array $additionalConfig = [])
    {
        if ($attributeConfig['visible'] == false
            || (isset($additionalConfig['visible']) && $additionalConfig['visible'] == false)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Check if field initially hidden
     *
     * @param string $attributeCode
     * @return bool
     */
    public function isAddressFieldInitiallyHidden($attributeCode)
    {
        return in_array($attributeCode, $this->initiallyHiddenAddressFields);
    }
}
