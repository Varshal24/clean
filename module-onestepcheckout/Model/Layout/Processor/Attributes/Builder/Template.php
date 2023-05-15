<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Builder;

use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiComponentBuilderInterface;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Address\TemplateResolver;

class Template implements UiComponentBuilderInterface
{
    /**
     * @var TemplateResolver
     */
    private $templateResolver;

    /**
     * @param TemplateResolver $templateResolver
     */
    public function __construct(TemplateResolver $templateResolver)
    {
        $this->templateResolver = $templateResolver;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build($code, $config, $definition = [], $additionalConfig = [])
    {
        $config = [
            'template' => 'Aheadworks_OneStepCheckout/form/field',
            'elementTmpl' => isset($additionalConfig['config']['elementTmpl'])
                ? $additionalConfig['config']['elementTmpl']
                : $this->templateResolver->resolve($config['formElement'])
        ];

        return array_merge_recursive($definition, ['config' => $config]);
    }
}
