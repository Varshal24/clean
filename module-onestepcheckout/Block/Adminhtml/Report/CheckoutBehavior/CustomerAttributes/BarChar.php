<?php
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior\CustomerAttributes;

use Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior\MultipleBarChart;

class BarChar extends MultipleBarChart
{
    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        $chartsData = $this->getChartsData();
        if (empty($chartsData)) {
            return '';
        }

        return parent::_toHtml();
    }
}
