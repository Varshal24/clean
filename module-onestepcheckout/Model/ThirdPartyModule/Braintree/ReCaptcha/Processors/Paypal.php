<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree\ReCaptcha\Processors;

use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree\ReCaptcha\ReCaptchaBraintreeProcessorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;

/**
 * Class Paypal
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status
 */
class Paypal implements ReCaptchaBraintreeProcessorInterface
{
    const XML_PATH_CONFIG_ACTIVE = 'payment/braintree/active';
    const MODULE_NAME = 'PayPal_Braintree';
    const COMPONENT_PATH = 'components/checkout/children/paymentMethod/children/methodList/children/braintree-recaptcha';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * Paypal constructor.
     * @param ModuleListInterface $moduleList
     * @param ProductMetadataInterface $productMetadata
     * @param ScopeConfigInterface $scopeConfig
     * @param ObjectManagerInterface $objectManager
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ModuleListInterface $moduleList,
        ProductMetadataInterface $productMetadata,
        ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectManager,
        ArrayManager $arrayManager
    ) {
        $this->moduleList = $moduleList;
        $this->scopeConfig = $scopeConfig;
        $this->productMetadata = $productMetadata;
        $this->objectManager = $objectManager;
        $this->arrayManager = $arrayManager;
    }

    /**
     * Check if module enabled
     *
     * @return bool
     */
    private function isEnabled()
    {
        return $this->moduleList->has(self::MODULE_NAME)
            && $this->scopeConfig->getValue(self::XML_PATH_CONFIG_ACTIVE);
    }

    /**
     * Check if module supported
     *
     * @return bool|int
     */
    private function isSupported()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        return version_compare($magentoVersion, '2.4.0', '>');
    }

    /**
     * @inheridoc
     */
    public function isAllowedProcessor()
    {
        return $this->isEnabled() && $this->isSupported();
    }

    /**
     * @inerhitDoc
     */
    public function process($jsLayout)
    {
        $captchaUiConfigResolver = $this->objectManager->get(UiConfigResolverInterface::class);
        $settings = $captchaUiConfigResolver->get('braintree');
        $componentRecaptchaBraintree = $this->arrayManager->get(self::COMPONENT_PATH, $jsLayout);
        $componentRecaptchaBraintree['children']['recaptcha_braintree']['settings'] = $settings;
        $componentRecaptchaBraintree = $this->addDefaultSetting($componentRecaptchaBraintree);
        $jsLayout = $this->arrayManager->set(self::COMPONENT_PATH, $jsLayout, $componentRecaptchaBraintree);

        return $this->arrayManager->remove(
            Magento::COMPONENT_PATH . '/children/msp_recaptcha_braintree',
            $jsLayout
        );
    }

    /**
     * Add default setting
     *
     * @return string[]
     */
    private function addDefaultSetting($componentRecaptchaBraintree)
    {
        return array_merge($componentRecaptchaBraintree, [
                'component' => 'uiComponent',
                'displayArea' => 'braintree-recaptcha'
            ]
        );
    }
}
