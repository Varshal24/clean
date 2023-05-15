<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate\DisplayOption as DisplayOptionSource;
use Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate\TimeSlot as TimeSlotSource;
use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\ConfigProvider\Date;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class DeliveryDate
 */
class DeliveryDate implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var TimeSlotSource
     */
    private $timeSlotSource;

    /**
     * @var Config
     */
    private $config;

    /**
     * The list of carrier's codes to skip delivery options
     * @var array
     */
    private $carrierCodeListToIgnore;

    /**
     * @var Date
     */
    private $dateConfigProvider;

    /**
     * @param ArrayManager $arrayManager
     * @param TimeSlotSource $timeSlotSource
     * @param Config $config
     * @param Date $dateConfigProvider
     * @param array $carrierCodeListToIgnore
     */
    public function __construct(
        ArrayManager $arrayManager,
        TimeSlotSource $timeSlotSource,
        Config $config,
        Date $dateConfigProvider,
        array $carrierCodeListToIgnore = []
    ) {
        $this->arrayManager = $arrayManager;
        $this->timeSlotSource = $timeSlotSource;
        $this->config = $config;
        $this->carrierCodeListToIgnore = $carrierCodeListToIgnore;
        $this->dateConfigProvider = $dateConfigProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        if ($this->config->getDeliveryDateDisplayOption() != DisplayOptionSource::NO) {
            $deliveryDatePath = 'components/checkout/children/shippingMethod/children/delivery-date/'
                . 'children/delivery-date-fieldset/children';

            $deliveryDateLayout = $this->arrayManager->get($deliveryDatePath, $jsLayout);
            if ($deliveryDateLayout) {
                if ($this->config->getDeliveryDateDisplayOption() == DisplayOptionSource::DATE_AND_TIME) {
                    $deliveryDateLayout = $this->addTimeOptions($deliveryDateLayout);
                } else {
                    $deliveryDateLayout = $this->hideTime($deliveryDateLayout);
                }
                $deliveryDateLayout = $this->addDateOptions($deliveryDateLayout);
                $jsLayout = $this->arrayManager->set($deliveryDatePath, $jsLayout, $deliveryDateLayout);
            }

            $jsLayout = $this->addCarrierCodeListToIgnore($jsLayout);
        }

        return $jsLayout;
    }

    /**
     * Add delivery time options
     *
     * @param array $layout
     * @return array
     */
    private function addTimeOptions(array $layout)
    {
        if (isset($layout['time'])) {
            $timeSlotOptions = $this->timeSlotSource->toOptionArray();
            $layout['time']['options'] = $timeSlotOptions;
            $layout['time']['visible'] = !empty($timeSlotOptions);
        }

        return $layout;
    }

    /**
     * Add delivery date options
     *
     * @param array $layout
     * @return array
     */
    private function addDateOptions(array $layout)
    {
        $options = [
            'dateFormat' => $this->dateConfigProvider->getDateFormat(),
            'firstDay' => $this->dateConfigProvider->getFirstDayOfWeek()
        ];

        $layout = $this->arrayManager->set('date/config/options', $layout, $options);

        return $layout;
    }

    /**
     * Hide time input
     *
     * @param array $layout
     * @return array
     */
    private function hideTime(array $layout)
    {
        if (isset($layout['time'])) {
            $layout['time']['visible'] = false;
        }

        return $layout;
    }

    /**
     * Add to the component config the list of carriers to skip delivery options
     *
     * @param array $jsLayout
     * @return array
     */
    private function addCarrierCodeListToIgnore(array $jsLayout)
    {
        $shippingMethodComponentPath = 'components/checkout/children/shippingMethod';
        $shippingMethodComponentData = $this->arrayManager->get(
            $shippingMethodComponentPath,
            $jsLayout,
            []
        );
        $shippingMethodComponentData['deliveryDateCarrierCodeListToIgnore'] = $this->carrierCodeListToIgnore;

        return $this->arrayManager->set(
            $shippingMethodComponentPath,
            $jsLayout,
            $shippingMethodComponentData
        );
    }
}
