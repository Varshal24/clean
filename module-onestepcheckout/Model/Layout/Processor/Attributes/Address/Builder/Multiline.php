<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Address\Builder;

use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiComponentBuilderInterface;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Address\TemplateResolver;
use Aheadworks\OneStepCheckout\Model\Config;

class Multiline implements UiComponentBuilderInterface
{
    /**
     * @var TemplateResolver
     */
    private $templateResolver;

    /**
     * @var Config
     */
    private $moduleConfig;

    /**
     * @param TemplateResolver $templateResolver
     * @param Config $moduleConfig
     */
    public function __construct(
        TemplateResolver $templateResolver,
        Config $moduleConfig
    ) {
        $this->templateResolver = $templateResolver;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build($code, $config, $definition = [], $additionalConfig = [])
    {
        $lines = [];
        $formConfig = $this->moduleConfig->getAddressFormConfig($additionalConfig['addressType']);
        $lineIndex = $additionalConfig['lineIndex'];
        $isFirstLine = $lineIndex === 0;
        if ($code == 'street') {
            $attributes = $formConfig['attributes']['street'] ?? [];
            if ((isset($attributes[$lineIndex]) && !$attributes[$lineIndex]['required']) || !$isFirstLine) {
                unset($config['validation']['required-entry']);
            }
        }

        $label = $additionalConfig['label'] ?? $config['label'];
        $line = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'label' => __($label . ' Line' . ($isFirstLine ? '' : ' ' . ($lineIndex + 1))),
            'config' => [
                'customScope' => $additionalConfig['customScope'],
                'template' => 'Aheadworks_OneStepCheckout/form/field-multiline',
                'elementTmpl' => $isFirstLine && $code == 'street'
                    ? 'Aheadworks_OneStepCheckout/form/element/input-autocomplete'
                    : $this->templateResolver->resolve('input')
            ],
            'customConfig' => [
                'formInputType' => 'input',
                'dataType' => 'text',
                'isSystem' => isset($config['system']) && $config['system'] == true
            ],
            'dataScope' => $lineIndex,
            'provider' => $additionalConfig['provider'],
            'validation' => $isFirstLine
                ? array_merge(['required-entry' => (bool)$config['required']], $config['validation'])
                : $config['validation']
        ];
        if ($isFirstLine && isset($config['defaultValue']) && $config['defaultValue'] != null) {
            $line['value'] = $config['defaultValue'];
        }
        $lines[$lineIndex] = $line;

        return [
            'component' => 'Magento_Ui/js/form/components/group',
            'required' => (bool)$config['required'],
            'dataScope' => $additionalConfig['customScope'] . '.' . $code,
            'provider' => $additionalConfig['provider'],
            'sortOrder' => $config['sortOrder'],
            'type' => 'group',
            'label' => '',
            'config' => [
                'template' => 'ui/group/group',
                'fieldTemplate' => 'Aheadworks_OneStepCheckout/form/field-multiline',
                'additionalClasses' => $code
            ],
            'children' => $lines,
        ];
    }
}
