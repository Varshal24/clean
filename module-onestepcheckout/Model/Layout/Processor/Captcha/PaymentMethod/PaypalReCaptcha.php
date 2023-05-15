<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Captcha\PaymentMethod;

use Aheadworks\OneStepCheckout\Model\Layout\DefinitionFetcher;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree\ReCaptcha\Processors\Paypal as PaypalReCaptchaBraintreeStatus;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Paypal\ReCaptcha\Block\LayoutProcessor\Checkout\OnepageFactory;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\MspReCaptcha as MspReCaptchaStatus;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\PaypalReCaptcha as PaypalReCaptchaStatus;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;

class PaypalReCaptcha implements LayoutProcessorInterface
{
    /**
     * @var MspReCaptchaStatus
     */
    private $mspReCaptchaStatus;

    /**
     * @var PaypalReCaptchaStatus
     */
    private $paypalReCaptchaStatus;

    /**
     * @var PaypalReCaptchaBraintreeStatus
     */
    private $paypalReCaptchaBraintreeStatus;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var DefinitionFetcher
     */
    private $definitionFetcher;

    /**
     * @var OnepageFactory
     */
    private $onepageFactory;

    /**
     * @param MspReCaptchaStatus $mspReCaptchaStatus
     * @param PaypalReCaptchaStatus $paypalReCaptchaStatus
     * @param ArrayManager $arrayManager
     * @param DefinitionFetcher $definitionFetcher
     * @param OnepageFactory $onepageFactory
     */
    public function __construct(
        MspReCaptchaStatus $mspReCaptchaStatus,
        PaypalReCaptchaStatus $paypalReCaptchaStatus,
        PaypalReCaptchaBraintreeStatus $paypalReCaptchaBraintreeStatus,
        ArrayManager $arrayManager,
        DefinitionFetcher $definitionFetcher,
        OnepageFactory $onepageFactory
    ) {
        $this->mspReCaptchaStatus = $mspReCaptchaStatus;
        $this->paypalReCaptchaStatus = $paypalReCaptchaStatus;
        $this->paypalReCaptchaBraintreeStatus = $paypalReCaptchaBraintreeStatus;
        $this->arrayManager = $arrayManager;
        $this->definitionFetcher = $definitionFetcher;
        $this->onepageFactory = $onepageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        if ($this->paypalReCaptchaBraintreeStatus->isAllowedProcessor()) {
            return $jsLayout;
        }
        $jsLayoutPaymentMethod = &$jsLayout['components']['checkout']['children']['paymentMethod'];
        if ($this->paypalReCaptchaStatus->getModuleName() == PaypalReCaptchaStatus::MODULE_NAME_V_2_3_X
            && $this->mspReCaptchaStatus->isEnabled()
            && $this->paypalReCaptchaStatus->isEnabled()
        ) {
            $path = $this->configurePath($jsLayoutPaymentMethod, 'msp_recaptcha');
        } elseif ($this->paypalReCaptchaStatus->getModuleName() == PaypalReCaptchaStatus::MODULE_NAME_V_2_4_X
            && $this->paypalReCaptchaStatus->isEnabled()
        ) {
            $path = $this->configurePath($jsLayoutPaymentMethod, 'recaptcha');
        }

        $onepage = $this->onepageFactory->create();
        if (isset($path) && $onepage) {
            $payPalJsLayout = $onepage->process([]);
            $ourLayout = $this->arrayManager->get($path['toOur'], $jsLayout);
            $nativeLayout = $this->arrayManager->get($path['fromNative'], $payPalJsLayout);

            if ($ourLayout && $nativeLayout) {
                $jsLayout = $this->arrayManager->merge($path['toOur'], $jsLayout, $nativeLayout);
            } elseif ($ourLayout && !$nativeLayout) {
                $jsLayout = $this->arrayManager->remove($path['toOur'], $jsLayout);
            }
        }

        return $jsLayout;
    }

    /**
     * Configure path
     *
     * @param array $jsLayoutPaymentMethod
     * @param string $recaptcha
     * @return string[]
     */
    private function configurePath(&$jsLayoutPaymentMethod, $recaptcha)
    {
        if (isset($jsLayoutPaymentMethod['children']['methodList']['children'])) {
            $this->addChildrenPayPalFields($jsLayoutPaymentMethod['children']['methodList']['children']);
        }

        return [
            'toOur' => 'components/checkout/children/paymentMethod/children/methodList/children/'
                . 'paypal-captcha/children/' . $recaptcha,
            'fromNative' => 'components/checkout/children/steps/children/billing-step/children/'
                . 'payment/children/payments-list/children/paypal-captcha/children/' . $recaptcha
        ];
    }

    /**
     * Add additional fields
     *
     * @param array $layout
     * @return void
     */
    private function addChildrenPayPalFields(array &$layout)
    {
        $path = '//referenceBlock[@name="checkout.root"]/arguments/argument[@name="jsLayout"]'
            . '/item[@name="components"]/item[@name="checkout"]/item[@name="children"]'
            . '/item[@name="steps"]/item[@name="children"]/item[@name="billing-step"]'
            . '/item[@name="children"]/item[@name="payment"]/item[@name="children"]'
            . '/item[@name="payments-list"]/item[@name="children"]';
        $definitions = $this->definitionFetcher->fetchArgs('checkout_index_index', $path);
        $layout = array_merge_recursive($layout, $definitions);
    }
}
