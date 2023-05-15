<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree\ReCaptcha;

/**
 * Interface ReCaptchaBraintreeInterface
 */
interface ReCaptchaBraintreeProcessorInterface
{
    /**
     * Check if allowed processor
     *
     * @return bool
     */
    public function isAllowedProcessor();

    /**
     * Retrieve ui config settings
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout);
}
