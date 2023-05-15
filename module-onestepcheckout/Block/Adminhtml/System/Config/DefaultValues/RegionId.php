<?php
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\DefaultValues;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

class RegionId extends Field
{
    /**
     * @inheritdoc
     *
     * We don't render element here.
     * It is used as value container, but element itself will be rendered in region field
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function render(AbstractElement $element)
    {
        return '';
    }
}
