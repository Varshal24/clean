<?php
namespace Aheadworks\OneStepCheckout\Model\DeliveryDate;

use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate\DisplayOption;

/**
 * Class ConfigProvider
 * @package Aheadworks\OneStepCheckout\Model\DeliveryDate
 */
class ConfigProvider
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get delivery date options config
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'isEnabled' => $this->config->getDeliveryDateDisplayOption() != DisplayOption::NO,
            'note' => [
                'isEnabled' => $this->config->isDeliveryDateNoteEnabled(),
                'text' => $this->config->getDeliveryDateNote()
            ],
            'dateRestrictions' => [
                'weekdays' => $this->config->getDeliveryDateAvailableWeekdays(),
                'nonDeliveryPeriods' => $this->config->getNonDeliveryPeriods(), // todo: to current timezone
                'minOrderDeliveryPeriod' => $this->config->getMinOrderDeliveryPeriod(),
                'sameDayDeliveryUnavailableAfter' => $this->config->getSameDayDeliveryUnavailableAfter(),
                'nextDayDeliveryUnavailableAfter' => $this->config->getNextDayDeliveryUnavailableAfter(),
                'timezone' => $this->config->getTimezone(),
                'estimatedDeliveryDate' => $this->config->getEstimatedDeliveryDate()
            ]
        ];
    }
}
