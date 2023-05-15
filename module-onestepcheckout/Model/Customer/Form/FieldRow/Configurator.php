<?php
namespace Aheadworks\OneStepCheckout\Model\Customer\Form\FieldRow;

use Aheadworks\OneStepCheckout\Model\Config as ModuleConfig;

/**
 * Prepare fields customization configuration
 */
class Configurator
{
    /**
     * @var array
     */
    private $configuration = [];

    /**
     * @var ModuleConfig
     */
    private $config;

    /**
     * @param ModuleConfig $config
     */
    public function __construct(
        ModuleConfig $config
    ) {
        $this->config = $config;
    }

    /**
     * Get customization configuration
     *
     * @return array
     */
    public function getCustomizationConfig()
    {
        if (empty($this->configuration)) {
            $this->configuration = [];
            $formConfig = $this->config->getCustomerInfoFormConfig();
            if (isset($formConfig['attributes']) && is_array($formConfig['attributes'])) {
                $rowCounter = 0;
                foreach ($formConfig['attributes'] as $attributeName => $attributeConfig) {
                    $sortOrder = $formConfig['rows'][$attributeName]['sort_order'] ?? 0;
                    if ($this->isNewRowRequired($attributeConfig)) {
                        $rowCounter ++;
                    }
                    $this->prepareAttributeField($rowCounter, $sortOrder, $attributeName);
                }
            }
        }

        return $this->configuration;
    }

    /**
     * Prepare attribute field
     *
     * @param int $rowCounter
     * @param string $sortOrder
     * @param string $attributeName
     */
    private function prepareAttributeField($rowCounter, $sortOrder, $attributeName)
    {
        $currentRowName = 'field-row-' . $rowCounter;
        $this->configuration['fields'][$attributeName] = [
            'row_name' => $currentRowName,
            'sort_order' => $sortOrder
        ];
        $this->configuration['sort_orders'][$currentRowName] = $rowCounter;
    }

    /**
     * Check if new row required
     *
     * @param array $attributeConfig
     * @return bool
     */
    private function isNewRowRequired($attributeConfig)
    {
        if (!isset($attributeConfig['is_moved'])) {
            return false;
        }

        return !(bool)$attributeConfig['is_moved'] && (bool)$attributeConfig['visible'];
    }
}
