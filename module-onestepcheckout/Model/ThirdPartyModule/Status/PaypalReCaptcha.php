<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Class PaypalReCaptcha
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status
 */
class PaypalReCaptcha
{
    const MODULE_NAME_V_2_3_X = 'Magento_PaypalReCaptcha';
    const MODULE_NAME_V_2_4_X = 'Magento_ReCaptchaPaypal';

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var string|null
     */
    private $moduleName = null;

    /**
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList
    ) {
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleList;
    }

    /**
     * Check if module enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->moduleList->has($this->getModuleName());
    }

    /**
     * Get module name for current magento version
     *
     * @return string
     */
    public function getModuleName()
    {
        if (!$this->moduleName) {
            $magentoVersion = $this->productMetadata->getVersion();
            $this->moduleName = version_compare($magentoVersion, '2.4.0', '<')
                ? self::MODULE_NAME_V_2_3_X
                : self::MODULE_NAME_V_2_4_X;
        }
        return $this->moduleName;
    }
}
