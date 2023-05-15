<?php
namespace Aheadworks\OneStepCheckout\Model\Data\Processor\Post;

/**
 * Class ProcessorComposite
 *
 * @package Aheadworks\OneStepCheckout\Model\Data\Processor\Post
 */
class ProcessorComposite implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        foreach ($this->processors as $processor) {
            $data = $processor->prepareEntityData($data);
        }
        return $data;
    }
}
