<?php
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Order;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Template;
use Magento\Framework\Registry;

/**
 * Class Note
 */
class Note extends Template
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Get current order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order') ?: $this->getData('order');
    }
}
