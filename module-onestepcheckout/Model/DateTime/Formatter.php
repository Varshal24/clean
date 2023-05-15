<?php
namespace Aheadworks\OneStepCheckout\Model\DateTime;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Catalog\Model\Product\Option\Type\Date;

/**
 * Class Formatter
 *
 * @package Aheadworks\OneStepCheckout\Model\DateTime
 */
class Formatter
{
    /**
     * @var TimezoneInterface
     */
    private $timeZoneResolver;

    /**
     * @var Date
     */
    private $catalogProductOptionTypeDate;

    /**
     * @param TimezoneInterface $timeZoneResolver
     * @param Date $catalogProductOptionTypeDate
     */
    public function __construct(
        TimezoneInterface $timeZoneResolver,
        Date $catalogProductOptionTypeDate
    ) {
        $this->timeZoneResolver = $timeZoneResolver;
        $this->catalogProductOptionTypeDate = $catalogProductOptionTypeDate;
    }

    /**
     * Get localized date/time
     *
     * @param string $date
     * @param int|null $time
     * @return string
     * @throws \Exception
     */
    public function getLocalizedDeliveryDateTime($date, $time = null)
    {
        $timezone = $this->timeZoneResolver->getConfigTimezone();
        $date = \DateTime::createFromFormat(
            $this->getDeliveryDateFormatToParseDate(),
            $date,
            new \DateTimeZone($timezone ?? '')
        )->setTime(0, 0, 0);
        if ($time) {
            $date->add(new \DateInterval('PT' . $time . 'S'));
        }

        return $this->timeZoneResolver->convertConfigTimeToUtc($date);
    }

    /**
     * Retrieve Delivery Date Format
     *
     * @return string|string[]
     */
    public function getDeliveryDateFormat()
    {
        $fieldsOrder = $this->catalogProductOptionTypeDate->getConfigData('date_fields_order');

        return str_replace([',', 'd', 'm'], ['/', 'dd', 'MM'], $fieldsOrder);
    }

    /**
     * Retrieve delivery date format to parse date
     *
     * @return string|string[]
     */
    public function getDeliveryDateFormatToParseDate()
    {
        $fieldsOrder = $this->catalogProductOptionTypeDate->getConfigData('date_fields_order');

        return str_replace([',', 'y'], ['/', 'Y'], $fieldsOrder);
    }

    /**
     * Retrieve formatted delivery date by applying timezone
     *
     * @param string $date
     * @param int $format
     * @param bool $showTime
     * @param string $timezone
     * @return string
     * @throws \Exception
     */
    public function getFormattedDateTimeWithTimezone($date, $format, $showTime, $timezone)
    {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date ?? 'now');
        return $this->timeZoneResolver->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
        );
    }
}
