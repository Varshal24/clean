<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\DeliveryDate\Renderer;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Aheadworks\OneStepCheckout\Model\Config\Source\ShippingMethod as ShippingMethodSource;

class ShippingMethod extends Select
{
    /**
    * Shipping group ids to remove
    */
    private const SHIPPING_GROUPS_TO_REMOVE = ['instore'];

    /**
     * @param ShippingMethodSource $shippingMethodSource
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        private ShippingMethodSource $shippingMethodSource,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Render HTML
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        $this->prepareOptions();
        $this->setClass('shipping-method-select');
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Prepare shipping options
     *
     * @return void
     */
    private function prepareOptions(): void
    {
        if (!$this->getOptions()) {
            $options = $this->shippingMethodSource->toOptionArray();
            foreach ($options as $optionGroup => $optionValue) {
                if (in_array($optionGroup, self::SHIPPING_GROUPS_TO_REMOVE)) {
                    unset($options[$optionGroup]);
                }
            }

            $this->setOptions($options);
        }
    }
}
