<?php
namespace Aheadworks\OneStepCheckout\Model\Captcha;

use Magento\Framework\App\ProductMetadataInterface;

class Checker
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ProductMetadataInterface $productMetadata
    ) {
        $this->productMetadata = $productMetadata;
    }

    /**
     * Check if native CAPTCHA protection has been added to the order placing action
     *
     * @return bool
     */
    public function isPlaceOrderCaptchaSupported()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        $isSupported23X = version_compare($magentoVersion, '2.3.6', '>=')
            && version_compare($magentoVersion, '2.4.0', '<');
        $isSupported24X = version_compare($magentoVersion, '2.4.1', '>=');

        return $isSupported23X || $isSupported24X;
    }
}
