<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Captcha;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\OneStepCheckout\Model\Captcha\Checker as CaptchaChecker;

class Common implements LayoutProcessorInterface
{
    /**
     * Path to the common component in the js layout array
     */
    const CAPTCHA_COMMON_COMPONENT_PATH
        = 'components/checkout/children/customer-information/children/aw-place-order-captcha';

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var CaptchaChecker
     */
    private $captchaChecker;

    /**
     * @param ArrayManager $arrayManager
     * @param CaptchaChecker $captchaChecker
     */
    public function __construct(
        ArrayManager $arrayManager,
        CaptchaChecker $captchaChecker
    ) {
        $this->arrayManager = $arrayManager;
        $this->captchaChecker = $captchaChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        if ($this->captchaChecker->isPlaceOrderCaptchaSupported()) {
            return $jsLayout;
        }

        return $this->removeCaptchaCommonComponent($jsLayout);
    }

    /**
     * Remove common UI component for placing order CAPTCHA protection
     *
     * @param array $jsLayout
     * @return array
     */
    private function removeCaptchaCommonComponent($jsLayout)
    {
        return $this->arrayManager->remove(
            self::CAPTCHA_COMMON_COMPONENT_PATH,
            $jsLayout
        );
    }
}
