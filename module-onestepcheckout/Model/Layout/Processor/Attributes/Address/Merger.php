<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Address;

use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\Mapper as FieldRowMapper;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiComponentBuilderInterface;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiConfigHelper;

/**
 * Address attributes merger
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
     * @var UiComponentBuilderInterface
     */
    private $multiLineBuilder;

    /**
     * @var UiConfigHelper
     */
    private $uiConfigHelper;

    /**
     * @param FieldRowMapper $fieldRowMapper
     * @param UiComponentBuilderInterface $builder
     * @param UiComponentBuilderInterface $multiLineBuilder
     * @param UiConfigHelper $uiConfigHelper
     */
    public function __construct(
        FieldRowMapper $fieldRowMapper,
        UiComponentBuilderInterface $builder,
        UiComponentBuilderInterface $multiLineBuilder,
        UiConfigHelper $uiConfigHelper
    ) {
        $this->fieldRowMapper = $fieldRowMapper;
        $this->builder = $builder;
        $this->multiLineBuilder = $multiLineBuilder;
        $this->uiConfigHelper = $uiConfigHelper;
    }

    /**
     * Merge additional address fields for given provider
     *
     * @param array $elements
     * @param string $providerName
     * @param string $addressType
     * @param array $fieldRows
     * @return array
     */
    public function merge($elements, $providerName, $addressType, array $fieldRows = [])
    {
        $rows = [];
        foreach ($elements as $attributeCode => $attributeConfig) {
            if ($attributeConfig['formElement'] == 'multiline') {
                for ($lineIndex = 0; $lineIndex < (int)$attributeConfig['size']; $lineIndex ++) {
                    $rows = $this->prepareRowConfig(
                        $attributeCode,
                        $attributeConfig,
                        $providerName,
                        $addressType,
                        $rows,
                        $fieldRows,
                        $lineIndex
                    );
                }
            } else {
                $rows = $this->prepareRowConfig(
                    $attributeCode,
                    $attributeConfig,
                    $providerName,
                    $addressType,
                    $rows,
                    $fieldRows
                );
            }
        }

        return $rows;
    }

    /**
     * Prepare row config
     *
     * @param string $code
     * @param array $config
     * @param string $providerName
     * @param string $addressType
     * @param array $rows
     * @param array $fieldRows
     * @param null|int $line
     * @return array
     */
    public function prepareRowConfig(
        $code,
        $config,
        $providerName,
        $addressType,
        array $rows = [],
        array $fieldRows = [],
        $line = null
    ) {
        $dataScopePrefix = $addressType . 'Address';
        $customAttributeRow = $code;
        if ($line !== null) {
            $customAttributeRow = $code . $line;
        }

        $defaultFieldRow = $this->fieldRowMapper->toDefaultFieldRow($code);
        $customFieldRow = $this->fieldRowMapper->toCustomFieldRow(
            $customAttributeRow,
            $addressType
        );
        $row = $customFieldRow ?? $defaultFieldRow ?? $code . '-field-row';

        if (!array_key_exists($row, $rows)) {
            $rows[$row] = !$customFieldRow && $defaultFieldRow && isset($fieldRows[$defaultFieldRow])
                ? $fieldRows[$defaultFieldRow]
                : $this->uiConfigHelper->getNewRowConfig();
        }

        $additionalConfig = $fieldRows[$defaultFieldRow]['children'][$code] ?? [];
        $additionalConfig['provider'] = $providerName;
        $additionalConfig['customScope'] = $dataScopePrefix;
        $additionalConfig['addressType'] = $addressType;
        if ($this->uiConfigHelper->isFieldVisible($config, $additionalConfig)
            || $this->uiConfigHelper->isAddressFieldInitiallyHidden($code)
        ) {
            if ($line !== null) {
                $additionalConfig['lineIndex'] = $line;
                $childFieldConfig = $this->multiLineBuilder->build($code, $config, [], $additionalConfig);
            } else {
                $childFieldConfig = $this->builder->build($code, $config, [], $additionalConfig);
            }

            $rows[$row]['children'][$customAttributeRow] = $childFieldConfig;
        }

        $this->uiConfigHelper->resolveRowTemplate($rows[$row]);

        return $rows;
    }
}
