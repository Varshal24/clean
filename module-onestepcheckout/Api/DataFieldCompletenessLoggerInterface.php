<?php
namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface DataFieldCompletenessLoggerInterface
 * @package Aheadworks\OneStepCheckout\Api
 */
interface DataFieldCompletenessLoggerInterface
{
    /**
     * Log guest checkout fields completeness data
     *
     * @param string $cartId
     * @param \Aheadworks\OneStepCheckout\Api\Data\DataFieldCompletenessInterface[] $fieldCompleteness
     * @return void
     */
    public function log($cartId, array $fieldCompleteness);
}
