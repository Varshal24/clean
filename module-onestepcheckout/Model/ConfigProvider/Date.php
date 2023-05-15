<?php
namespace Aheadworks\OneStepCheckout\Model\ConfigProvider;

use Aheadworks\OneStepCheckout\Model\DateTime\Formatter as DateFormatter;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Date
 */
class Date
{
    /**
     * Configuration path to locale firstday
     */
    const XML_PATH_LOCALE_FIRSTDAY = 'general/locale/firstday';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var DateFormatter
     */
    private $dateFormatter;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param DateFormatter $dateFormatter
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        DateFormatter $dateFormatter
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->dateFormatter = $dateFormatter;
    }

    /**
     * Get first day of week
     *
     * @return int
     */
    public function getFirstDayOfWeek()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_LOCALE_FIRSTDAY);
    }

    /**
     * Retrieve Delivery Date Format
     *
     * @return string|string[]
     */
    public function getDateFormat()
    {
        return $this->dateFormatter->getDeliveryDateFormat();
    }
}
