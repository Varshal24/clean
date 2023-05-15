<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes;

/**
 * Uses to build ui component definition for specified attribute
 */
interface UiComponentBuilderInterface
{
    /**
     * Build ui component definition for attribute
     *
     * @param string $code
     * @param array $config
     * @param array $definition
     * @param array $additionalConfig
     * @return array
     */
    public function build($code, $config, $definition = [], $additionalConfig = []);
}
