<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Customer\Builder;

use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiComponentBuilderInterface;

class CustomerSpecific implements UiComponentBuilderInterface
{
    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build($code, $config, $definition = [], $additionalConfig = [])
    {
        if (isset($additionalConfig['customScope'])) {
            $definition['config']['customScope'] = $additionalConfig['customScope'];
            $dataScope = isset($config['backendType']) && $config['backendType'] == 'static'
                ? $additionalConfig['customScope'] . '.' . $code
                : $additionalConfig['customScope'] . '.custom_attributes.' . $code;
            $definition['dataScope'] = $dataScope;
        }

        $definition['provider'] = $additionalConfig['provider'] ?? '';

        return $definition;
    }
}
