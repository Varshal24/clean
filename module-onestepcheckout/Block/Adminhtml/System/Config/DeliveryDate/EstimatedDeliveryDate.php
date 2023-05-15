<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\DeliveryDate;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\DeliveryDate\Renderer\ShippingMethod;

class EstimatedDeliveryDate extends AbstractFieldArray
{
    /**
     * @var ShippingMethod
     */
    private $shippingMethodsRenderer;

    /**
     * Prepare to render
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'shipping_method',
            [
                'label' => __('Shipping Method'),
                'renderer' => $this->getShippingMethodRenderer()
            ]
        );
        $this->addColumn(
            'number_of_days',
            [
                'label' => __('Number of Days'),
                'class' => 'required-entry validate-greater-than-zero'
            ]
        );
        $this->_addAfter = false;
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $shippingMethodRenderer = $this->getShippingMethodRenderer();
        $row->setData(
            'option_extra_attrs',
            [
                'option_' . $shippingMethodRenderer->calcOptionHash($row->getShippingMethod()) => 'selected="selected"'
            ]
        );
    }

    /**
     * Get Shipping Method Renderer
     *
     * @return ShippingMethod
     * @throws LocalizedException
     */
    private function getShippingMethodRenderer(): ShippingMethod
    {
        if (!$this->shippingMethodsRenderer) {
            $this->shippingMethodsRenderer = $this->getLayout()->createBlock(
                ShippingMethod::class,
                '',
                [
                    'data' => [
                        'is_render_to_js_template' => true,
                        'class' => 'required-entry'
                    ]
                ]
            );
        }
        return $this->shippingMethodsRenderer;
    }
}
