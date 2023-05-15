<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Builder;

use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiComponentBuilderInterface;

class Common implements UiComponentBuilderInterface
{
    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build($code, $config, $definition = [], $additionalConfig = [])
    {
        $data = [
            'config' => [
                'customEntry' => $additionalConfig['config']['customEntry'] ?? null,
                'tooltip' => $additionalConfig['config']['tooltip'] ?? null
            ],
            'customConfig' => [
                'formInputType' => $config['formElement'],
                'dataType' => $config['dataType'],
                'isSystem' => isset($config['system']) && $config['system'] == true
            ],
            'label' => $config['label'],
            'sortOrder' => $additionalConfig['sortOrder'] ?? $config['sortOrder'],
            'options' => $config['options'] ?? [],
            'filterBy' => $additionalConfig['filterBy'] ?? null,
            'customEntry' => $additionalConfig['customEntry'] ?? null,
            'imports' => $additionalConfig['imports'] ?? null,
        ];

        if (isset($additionalConfig['visible'])) {
            $data['visible'] = $additionalConfig['visible'];
        } else {
            $data['visible'] = true;
        }

        if (isset($config['value']) && $config['value'] != null) {
            $data['value'] = $config['value'];
        } elseif (isset($config['defaultValue']) && $config['defaultValue'] != null) {
            $data['value'] = $config['defaultValue'];
        }

        if (isset($additionalConfig['config']['additionalClasses'])) {
            $data['config']['additionalClasses'] = $additionalConfig['config']['additionalClasses'];
        }

        return array_merge_recursive($definition, $data);
    }
}
