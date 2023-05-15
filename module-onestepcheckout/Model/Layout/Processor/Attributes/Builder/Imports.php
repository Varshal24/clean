<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Builder;

use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiComponentBuilderInterface;

class Imports implements UiComponentBuilderInterface
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
    public function build($code, $config, $definition = [], $additionalConfig = []): array
    {
        $imports = $additionalConfig['config']['imports'] ?? null;

        if ($imports) {
            $config = [
                'imports' => $imports
            ];

            $definition = array_merge_recursive($definition, ['config' => $config]);
        }

        return $definition;
    }
}
