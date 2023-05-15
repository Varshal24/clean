<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Paypal\ReCaptcha\Model;

use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\PaypalReCaptcha as PaypalReCaptchaStatus;
use Magento\Framework\ObjectManagerInterface;
use Magento\ReCaptchaPaypal\Model\CheckoutConfigProvider as PaypalCheckoutConfigProvider;

/**
 * Class CheckoutConfigProvider
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Paypal\ReCaptcha\Model
 */
class CheckoutConfigProviderFactory
{
    /**
     * @var PaypalReCaptchaStatus
     */
    private $paypalReCaptchaStatus;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param PaypalReCaptchaStatus $paypalReCaptchaStatus
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        PaypalReCaptchaStatus $paypalReCaptchaStatus,
        ObjectManagerInterface $objectManager
    ) {
        $this->paypalReCaptchaStatus = $paypalReCaptchaStatus;
        $this->objectManager = $objectManager;
    }

    /**
     * Create object
     *
     * @return PaypalCheckoutConfigProvider|null
     */
    public function create()
    {
        return $this->paypalReCaptchaStatus->isEnabled()
            && $this->paypalReCaptchaStatus->getModuleName() == PaypalReCaptchaStatus::MODULE_NAME_V_2_4_X
                ? $this->objectManager->create(PaypalCheckoutConfigProvider::class)
                : null;
    }
}
