<?php
namespace Aheadworks\OneStepCheckout\Model\Config\Source\Customization;

class BooleanMetaField
{
    /**#@+
     * Boolean metadata fields
     */
    const VISIBLE = 'visible';
    const REQUIRED = 'required';
    const IS_MOVED = 'is_moved';
    /**#@-*/

    /**
     * Get metadata fields with boolean values
     *
     * @return array
     */
    public function getFields()
    {
        return [
            self::VISIBLE => 'Enable',
            self::REQUIRED => 'Required',
            self::IS_MOVED => 'Place near previous field'
        ];
    }
}
