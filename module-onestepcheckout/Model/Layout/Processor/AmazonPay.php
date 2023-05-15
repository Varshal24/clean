<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\AmazonPay as AmazonStatus;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class AmazonPay
 */
class AmazonPay implements LayoutProcessorInterface
{
    const AMAZON_PAY_ADDRESS_PATH
        = 'components/checkout/children/shippingAddress/children/amazon-pay-address';
    const AMAZON_PAY_BUTTON_REGION_PATH
        = 'components/checkout/children/shippingAddress/children/amazon-pay-button-region';

    /**
     * @var AmazonStatus
     */
    private $amazonStatus;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param AmazonStatus $amazonStatus
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        AmazonStatus $amazonStatus,
        ArrayManager $arrayManager
    ) {
        $this->amazonStatus = $amazonStatus;
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        $componentPaths = [
            'components/checkout/children/shippingAddress/component' =>
                'Aheadworks_OneStepCheckout/js/view/shipping-address/address-renderer/amazon-pay-renderer',
            'components/checkout/children/shippingAddress/children/shipping-address-list/component' =>
                'Aheadworks_OneStepCheckout/js/view/shipping-address/amazon-pay-list',
            'components/checkout/children/paymentMethod/children/methodList/component' =>
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/amazon-pay/list',
            'components/checkout/children/paymentMethod/children/billingAddress/component' =>
                'Aheadworks_OneStepCheckout/js/view/billing-address/address-renderer/amazon-pay-renderer',
            'components/checkout/children/paymentMethod/children/billingAddress/children/billing-address-list/component' =>
                'Aheadworks_OneStepCheckout/js/view/billing-address/amazon-pay-list'
        ];

        if ($this->amazonStatus->isEnabled()) {
            foreach ($componentPaths as $path => $option) {
                $jsLayout = $this->arrayManager->set($path, $jsLayout, $option);
            }
        } else {
            $jsLayout = $this->arrayManager->remove(self::AMAZON_PAY_ADDRESS_PATH, $jsLayout);
            $jsLayout = $this->arrayManager->remove(self::AMAZON_PAY_BUTTON_REGION_PATH, $jsLayout);
        }

        return $jsLayout;
    }
}
