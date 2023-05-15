<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes;

/**
 * Uses to build ui component definition in composition
 */
class UiComponentBuilderComposite implements UiComponentBuilderInterface
{
    /**
     * @var UiComponentBuilderInterface[]
     */
    private $builders;

    /**
     * @param UiComponentBuilderInterface[] $builders
     */
    public function __construct(array $builders = [])
    {
        $this->builders = $builders;
    }

    /**
     * @inheritdoc
     */
    public function build($code, $config, $definition = [], $additionalConfig = [])
    {
        $definition = [];
        foreach ($this->builders as $builder) {
            if (!$builder instanceof UiComponentBuilderInterface) {
                throw new \InvalidArgumentException(
                    sprintf('Builder instance does not extend %s.', UiComponentBuilderInterface::class)
                );
            }
            $definition = $builder->build($code, $config, $definition, $additionalConfig);
        }

        return $definition;
    }
}
