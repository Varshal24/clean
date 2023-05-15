<?php
namespace Aheadworks\OneStepCheckout\Model\Data\Processor\Model;

/**
 * Class ProcessorComposite
 *
 * @package Aheadworks\OneStepCheckout\Model\Data\Processor\Model
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
    public function prepareModelBeforeSave($model)
    {
        foreach ($this->processors as $processor) {
            $processor->prepareModelBeforeSave($model);
        }

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function prepareModelAfterLoad($model)
    {
        foreach ($this->processors as $processor) {
            $processor->prepareModelAfterLoad($model);
        }

        return $model;
    }
}
