<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate;

class TimeWithEmptyValue extends Time
{
    /**
     * @var array
     */
    private $options;

    /**
     * Get time intervals with first empty value
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if (!$this->options) {
            $this->options = array_merge(
                [
                    ['value' => 'empty', 'label' => __('-- Please Select --')]
                ],
                parent::toOptionArray()
            );
        }

        return $this->options;
    }
}
