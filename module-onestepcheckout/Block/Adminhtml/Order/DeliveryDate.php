<?php
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Order;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Aheadworks\OneStepCheckout\Model\DateTime\Formatter as DateTimeFormatter;

/**
 * Class DeliveryDate
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\Order
 */
class DeliveryDate extends AbstractDeliveryDate
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var DateTimeFormatter
     */
    private $dateTimeFormatter;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     * @param DateTimeFormatter $dateTimeFormatter
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        DateTimeFormatter $dateTimeFormatter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    /**
     * {@inheritdoc}
     */
    protected function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
    ) {
        $store = $this->getOrder()->getStore();
        $timezone = $timezone ?: $this->_localeDate->getConfigTimezone(ScopeInterface::SCOPE_STORE, $store->getCode());

        return $this->dateTimeFormatter->getFormattedDateTimeWithTimezone($date, $format, $showTime, $timezone);
    }
}
