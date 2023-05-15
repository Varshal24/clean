<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\ModuleManager;

/**
 * Class MergedGiftCardAndDiscount
 *
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor
 */
class MergedGiftCardAndDiscount implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @param ArrayManager $arrayManager
     * @param ModuleManager $moduleManager
     * @param Config $config
     */
    public function __construct(
        ArrayManager $arrayManager,
        ModuleManager $moduleManager,
        Config $config
    ) {
        $this->arrayManager = $arrayManager;
        $this->moduleManager = $moduleManager;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout)
    {
        if ($this->config->isEnabledGcAndCouponCodeFieldMerge()) {
            if ($this->moduleManager->isAheadworksGiftCardEnabled()) {
                return $this->mergeAwGiftCardAndDiscount($jsLayout);
            }
            if ($this->moduleManager->isMagentoGiftCardEnabled()) {
                return $this->mergeMagentoGiftCardAndDiscount($jsLayout);
            }
        }

        return $jsLayout;
    }

    /**
     * Merge Aheadworks Gift Card and Coupon Code into one field
     *
     * @param array $jsLayout
     * @return array
     */
    private function mergeAwGiftCardAndDiscount($jsLayout)
    {
        $mergedFieldLayout = [
            'component' => 'Aheadworks_OneStepCheckout/js/view/sidebar/payment-option/merged-aw-gc-discount',
            'sortOrder' => '20'
        ];
        $jsLayout = $this->arrayManager->replace(
            'components/checkout/children/payment-options/children/aw-giftcard',
            $jsLayout,
            $mergedFieldLayout
        );
        $jsLayout = $this->arrayManager->remove(
            'components/checkout/children/payment-options/children/discount',
            $jsLayout
        );

        return $jsLayout;
    }

    /**
     * Merge Aheadworks Gift Card and Coupon Code into one field
     *
     * @param array $jsLayout
     * @return array
     */
    private function mergeMagentoGiftCardAndDiscount($jsLayout)
    {
        $mergedFieldLayout = [
            'component' => 'Aheadworks_OneStepCheckout/js/view/sidebar/payment-option/merged-ee-gc-discount',
            'sortOrder' => '20'
        ];
        $jsLayout = $this->arrayManager->replace(
            'components/checkout/children/payment-options/children/giftCardAccount',
            $jsLayout,
            $mergedFieldLayout
        );
        $jsLayout = $this->arrayManager->remove(
            'components/checkout/children/payment-options/children/discount',
            $jsLayout
        );

        return $jsLayout;
    }
}
