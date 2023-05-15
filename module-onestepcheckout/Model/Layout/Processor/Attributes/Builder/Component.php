<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Builder;

use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiComponentBuilderInterface;

class Component implements UiComponentBuilderInterface
{
    /**
     * @var array
     */
    private $formElementMap = [
        'checkbox' => 'Magento_Ui/js/form/element/select',
        'select' => 'Magento_Ui/js/form/element/select',
        'textarea'  => 'Magento_Ui/js/form/element/textarea',
        'multiline' => 'Magento_Ui/js/form/components/group',
        'multiselect' => 'Magento_Ui/js/form/element/multiselect',
    ];

    /**
     * @param array $formElementMap
     */
    public function __construct(array $formElementMap = [])
    {
        $this->formElementMap = array_merge($this->formElementMap, $formElementMap);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build($code, $config, $definition = [], $additionalConfig = [])
    {
        $uiComponent = $this->formElementMap[$config['formElement']]
            ?? 'Magento_Ui/js/form/element/abstract';

        $definition['component'] = isset($additionalConfig['component'])
            ? $additionalConfig['component']
            : $uiComponent;

        return $definition;
    }
}
