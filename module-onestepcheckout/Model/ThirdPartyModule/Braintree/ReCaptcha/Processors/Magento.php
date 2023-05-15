<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree\ReCaptcha\Processors;

use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree\ReCaptcha\ReCaptchaBraintreeProcessorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\ArrayManager;
use MSP\ReCaptcha\Model\LayoutSettings;

/**
 * Class Magento
 */
class Magento implements ReCaptchaBraintreeProcessorInterface
{
    const XML_PATH_CONFIG_ACTIVE = 'payment/braintree/active';
    const XML_PATH_CONFIG_ENABLE_RECAPTCHA = 'payment/braintree/enable_recaptcha';
    const MODULE_NAME = 'Magento_Braintree';
    const COMPONENT_PATH = 'components/checkout/children/paymentMethod/children/methodList/children/braintree-recaptcha';

    /**
     * @var ModuleList
     */
    private $moduleList;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ModuleList $moduleList
     * @param ScopeConfigInterface $scopeConfig
     * @param ObjectManagerInterface $objectManager
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ModuleList $moduleList,
        ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectManager,
        ArrayManager $arrayManager
    ) {
        $this->moduleList = $moduleList;
        $this->scopeConfig = $scopeConfig;
        $this->objectManager = $objectManager;
        $this->arrayManager = $arrayManager;
    }

    /**
     * @inerhitDoc
     */
    private function isEnabled()
    {
        return $this->moduleList->has(self::MODULE_NAME)
            && $this->scopeConfig->getValue(self::XML_PATH_CONFIG_ACTIVE);
    }

    /**
     * @inerhitDoc
     */
    private function isSupported()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CONFIG_ENABLE_RECAPTCHA);
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
        $componentRecaptchaBraintree = $this->arrayManager->get(self::COMPONENT_PATH, $jsLayout);
        $componentRecaptchaBraintree['children']['msp_recaptcha_braintree']['settings'] = $this->objectManager->get(LayoutSettings::class)->getCaptchaSettings();
        $componentRecaptchaBraintree = $this->addDefaultSetting($componentRecaptchaBraintree);
        $jsLayout = $this->arrayManager->set(self::COMPONENT_PATH, $jsLayout, $componentRecaptchaBraintree);

        return $this->arrayManager->remove(
            Paypal::COMPONENT_PATH . '/children/recaptcha_braintree',
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
