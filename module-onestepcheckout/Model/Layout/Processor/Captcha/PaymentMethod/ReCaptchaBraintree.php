<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Captcha\PaymentMethod;

use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree\ReCaptcha\ReCaptchaBraintreeCompositeProcessor;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;

class ReCaptchaBraintree implements LayoutProcessorInterface
{
    /**
     * @var ReCaptchaBraintreeCompositeProcessor
     */
    private $reCaptchaBraintreeCompositeProcessor;

    /**
     * ReCaptchaBraintree constructor.
     * @param ReCaptchaBraintreeCompositeProcessor $reCaptchaBraintreeCompositeProcessor
     */
    public function __construct(
        ReCaptchaBraintreeCompositeProcessor $reCaptchaBraintreeCompositeProcessor
    ) {
        $this->reCaptchaBraintreeCompositeProcessor = $reCaptchaBraintreeCompositeProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        $processor = $this->reCaptchaBraintreeCompositeProcessor->getProcessor();
        if ($processor) {
            try {
                $jsLayout = $processor->process($jsLayout);
            } catch (InputException $exception) {
                unset($jsLayout['components']['checkout']['children']
                    ['paymentMethod']['children']['methodList']
                    ['children']['braintree-recaptcha']);
            }
        } else {
            unset($jsLayout['components']['checkout']['children']
                ['paymentMethod']['children']['methodList']
                ['children']['braintree-recaptcha']);
        }

        return $jsLayout;
    }
}
