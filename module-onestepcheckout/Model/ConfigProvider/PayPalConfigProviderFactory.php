<?php
namespace Aheadworks\OneStepCheckout\Model\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class PayPalConfigProviderFactory
 * @package Aheadworks\OneStepCheckout\Model\ConfigProvider
 */
class PayPalConfigProviderFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ProductMetadataInterface $productMetadata
    ) {
        $this->objectManager = $objectManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Create serializer instance
     *
     * @return ConfigProviderInterface
     */
    public function create()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        $paypalConfigProvider = version_compare($magentoVersion, '2.4.0', '>=')
            ? \PayPal\Braintree\Model\Ui\PayPal\ConfigProvider::class
            : \Magento\Braintree\Model\Ui\PayPal\ConfigProvider::class;

        return $this->objectManager->create($paypalConfigProvider);
    }
}
