<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree\ReCaptcha;

/**
 * Class ReCaptchaBraintreeCompositeProcessor
 */
class ReCaptchaBraintreeCompositeProcessor
{
    /**
     * @var array
     */
    private $processors;

    /**
     * @param $processors
     */
    public function __construct(
        $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * Retrieve recaptcha braintree processor
     *
     * @return mixed|null
     */
    public function getProcessor()
    {
        foreach ($this->processors as $processor) {
            if ($processor instanceof ReCaptchaBraintreeProcessorInterface && $processor->isAllowedProcessor()) {
                break;
            }

            $processor = null;
        }

        return $processor;
    }
}
