<?php
namespace Aheadworks\OneStepCheckout\Model\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree\Status as BraintreeModuleStatus;

/**
 * Class PaymentMethodExtended
 *
 * @package Aheadworks\OneStepCheckout\Model\ConfigProvider
 */
class PaymentMethodExtended implements ConfigProviderInterface
{
    /**
     * @var BraintreeModuleStatus
     */
    private $braintreeModuleStatus;

    /**
     * @param PayPalConfigProviderFactory $paypalConfigProvider
     */
    private $paypalConfigProvider;

    /**
     * @param BraintreeModuleStatus $braintreeModuleStatus
     * @param PayPalConfigProviderFactory $paypalConfigProvider
     */
    public function __construct(
        BraintreeModuleStatus $braintreeModuleStatus,
        PayPalConfigProviderFactory $paypalConfigProvider
    ) {
        $this->braintreeModuleStatus = $braintreeModuleStatus;
        $this->paypalConfigProvider = $paypalConfigProvider->create();
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                $this->paypalConfigProvider::PAYPAL_CODE => [
                    'awOscIsContextCheckout' => $this->braintreeModuleStatus->isPayPalInContextMode()
                ]
            ]
        ];
    }
}
