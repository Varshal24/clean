<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Customer;

use Aheadworks\OneStepCheckout\Model\Customer\Form\FieldRow\Mapper as FieldRowMapper;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiComponentBuilderInterface;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiConfigHelper;

/**
 * Customer attributes merger
 */
class Merger
{
    /**
     * @var FieldRowMapper
     */
    private $fieldRowMapper;

    /**
     * @var UiComponentBuilderInterface
     */
    private $builder;

    /**
     * @var UiConfigHelper
     */
    private $uiConfigHelper;

    /**
     * @param FieldRowMapper $fieldRowMapper
     * @param UiComponentBuilderInterface $builder
     * @param UiConfigHelper $uiConfigHelper
     */
    public function __construct(
        FieldRowMapper $fieldRowMapper,
        UiComponentBuilderInterface $builder,
        UiConfigHelper $uiConfigHelper
    ) {
        $this->fieldRowMapper = $fieldRowMapper;
        $this->builder = $builder;
        $this->uiConfigHelper = $uiConfigHelper;
    }

    /**
     * Merge additional customer info fields for given provider
     *
     * @param array $elements
     * @param string $providerName
     * @return array
     */
    public function merge($elements, $providerName)
    {
        $rows = [];
        $additionalConfig = [
            'provider' => $providerName,
            'customScope' => 'customerInfo'
        ];
        foreach ($elements as $attributeCode => $attributeConfig) {
            if ($this->uiConfigHelper->isFieldVisible($attributeConfig)) {
                $childFieldConfig = $this->builder->build($attributeCode, $attributeConfig, [], $additionalConfig);
                if ($this->uiConfigHelper->isFieldVisible($childFieldConfig)) {
                    $customFieldRow = $this->fieldRowMapper->toCustomFieldRow($attributeCode);
                    $row = $customFieldRow ?? $attributeCode . '-field-row';

                    if (!array_key_exists($row, $rows)) {
                        $rows[$row] = $this->uiConfigHelper->getNewRowConfig();
                    }

                    $rows[$row]['children'][$attributeCode] = $childFieldConfig;
                    $this->uiConfigHelper->resolveRowTemplate($rows[$row]);
                }
            }
        }

        return $rows;
    }
}
