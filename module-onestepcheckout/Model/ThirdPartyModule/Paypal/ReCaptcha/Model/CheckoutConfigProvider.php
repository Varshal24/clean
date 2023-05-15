<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Paypal\ReCaptcha\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class CheckoutConfigProvider
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Paypal\ReCaptcha\Model
 */
class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * @var CheckoutConfigProviderFactory
     */
    private $checkoutConfigProviderFactory;

    /**
     * @param CheckoutConfigProviderFactory $checkoutConfigProviderFactory
     */
    public function __construct(
        CheckoutConfigProviderFactory $checkoutConfigProviderFactory
    ) {
        $this->checkoutConfigProviderFactory = $checkoutConfigProviderFactory;
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        $paypalCheckoutConfigProvider = $this->checkoutConfigProviderFactory->create();
        return $paypalCheckoutConfigProvider
            ? $paypalCheckoutConfigProvider->getConfig()
            : [];
    }
}
