<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Builder;

use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiComponentBuilderInterface;

class Validation implements UiComponentBuilderInterface
{
    /**
     * @var array
     */
    private $inputValidationMap = [
        'alpha' => 'validate-alpha',
        'numeric' => 'validate-number',
        'alphanumeric' => 'validate-alphanum',
        'url' => 'validate-url',
        'email' => 'email2',
        'length' => 'validate-length',
        'alphanum-with-spaces' => 'validate-alphanum-with-spaces'
    ];

    /**
     * @param array $inputValidationMap
     */
    public function __construct(array $inputValidationMap = [])
    {
        $this->inputValidationMap = array_merge($this->inputValidationMap, $inputValidationMap);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build($code, $config, $definition = [], $additionalConfig = [])
    {
        if (isset($config['validation']['input_validation'])) {
            $validationRule = $config['validation']['input_validation'];
            if (isset($this->inputValidationMap[$validationRule])) {
                $config['validation'][$this->inputValidationMap[$validationRule]] = true;
                unset($config['validation']['input_validation']);
            }
        }

        return array_merge_recursive(
            $definition,
            [
                'validation' => $this->mergeConfigurationNode('validation', $additionalConfig, $config)
            ]
        );
    }

    /**
     * Merge two configuration nodes recursively
     *
     * @param string $nodeName
     * @param array $mainSource
     * @param array $additionalSource
     * @return array
     */
    public function mergeConfigurationNode($nodeName, array $mainSource, array $additionalSource)
    {
        $mainData = isset($mainSource[$nodeName]) ? $mainSource[$nodeName] : [];
        $additionalData = isset($additionalSource[$nodeName]) ? $additionalSource[$nodeName] : [];

        return array_replace_recursive($additionalData, $mainData);
    }
}
