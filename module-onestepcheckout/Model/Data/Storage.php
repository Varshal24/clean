<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\Data;

/**
 * Class Storage
 */
class Storage
{
    /**#@+
     * Constants defined for keys of the data array.
     */
    CONST IS_SHOULD_CREATED_ACCOUNT = 'is_should_created_account';
    /**#@-*/

    /**
     * @var array
     */
    protected $_data = [];

    /**
     * Add data to storage
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function add(string $key, $value): void
    {
        $this->_data[$key] = $value;
    }

    /**
     * Retrieve data from storage
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->_data[$key] ?? null;
    }
}