<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Paypal\ReCaptcha\Block\LayoutProcessor\Checkout;

use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\PaypalReCaptcha as PaypalReCaptchaStatus;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\PaypalReCaptcha\Block\LayoutProcessor\Checkout\Onepage as PaypalReCaptchaOnepage;
use Magento\ReCaptchaPaypal\Block\LayoutProcessor\Checkout\Onepage as ReCaptchaPaypalOnepage;

/**
 * Class OnepageFactory
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Paypal\ReCaptcha\Block\LayoutProcessor\Checkout
 */
class OnepageFactory
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var PaypalReCaptchaStatus
     */
    private $paypalReCaptchaStatus;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ProductMetadataInterface $productMetadata
     * @param PaypalReCaptchaStatus $paypalReCaptchaStatus
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        PaypalReCaptchaStatus $paypalReCaptchaStatus,
        ObjectManagerInterface $objectManager
    ) {
        $this->productMetadata = $productMetadata;
        $this->paypalReCaptchaStatus = $paypalReCaptchaStatus;
        $this->objectManager = $objectManager;
    }

    /**
     * Create object
     *
     * @return LayoutProcessorInterface|null
     */
    public function create()
    {
        if ($this->paypalReCaptchaStatus->isEnabled()) {
            $magentoVersion = $this->productMetadata->getVersion();
            $className = version_compare($magentoVersion, '2.4.0', '<')
                ? PaypalReCaptchaOnepage::class
                : ReCaptchaPaypalOnepage::class;
            $result = $this->objectManager->create($className);
        } else {
            $result = null;
        }

        return $result;
    }
}
