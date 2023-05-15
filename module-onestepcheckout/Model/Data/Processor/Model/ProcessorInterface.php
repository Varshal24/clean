<?php
namespace Aheadworks\OneStepCheckout\Model\Data\Processor\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\OneStepCheckout\Model\Data\Processor\Model
 */
interface ProcessorInterface
{
    /**
     * Prepare model before save
     *
     * @param AbstractModel $model
     * @return AbstractModel
     */
    public function prepareModelBeforeSave($model);

    /**
     * Prepare model after load
     *
     * @param AbstractModel $model
     * @return AbstractModel
     */
    public function prepareModelAfterLoad($model);
}
