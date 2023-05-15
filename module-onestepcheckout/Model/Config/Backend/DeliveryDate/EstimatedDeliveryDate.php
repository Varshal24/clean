<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\Config\Backend\DeliveryDate;

use Aheadworks\OneStepCheckout\Model\Config\Backend\ConfigValue;

class EstimatedDeliveryDate extends ConfigValue
{
    /**
     * Processing object before save data
     *
     * @return $this
     */
    public function beforeSave(): self
    {
        $result = [];
        $value = $this->resolveSerializedValue();
        if (is_array($value)) {
            foreach ($value as $data) {
                if ((isset($data['shipping_method']) && (!empty($data['shipping_method'])))
                    && (isset($data['number_of_days']) && (!empty($data['number_of_days'])))
                ) {
                    $result[] = $data;
                }
            }
        }

        $this->setValue($this->serializer->serialize($result));

        return $this;
    }

    /**
     * Process data after load
     *
     * @return $this
     */
    public function afterLoad(): self
    {
        if (empty($this->getValue())) {
            return $this;
        }
        $value = $this->serializer->unserialize($this->getValue());

        if (is_array($value)) {
            $value = array_combine(
                array_map(
                    function ($key) {
                        return 'option_' . $key;
                    },
                    array_keys($value)
                ),
                $value
            );

            $this->setValue($value);
        }
        return $this;
    }
}
