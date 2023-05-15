<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Class ModuleManager
 *
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule
 */
class ModuleManager
{
    const KLARNA_KP_MODULE_NAME = 'Klarna_Kp';
    const AW_GIFTCARD_MODULE_NAME = 'Aheadworks_Giftcard';
    const AW_CUSTOMER_ATTRIBUTES = 'Aheadworks_CustomerAttributes';
    const MAGENTO_GIFTCARD_MODULE_NAME = 'Magento_GiftCardAccount';
    const MAGENTO_CUSTOMER_ATTRIBUTES = 'Magento_CustomerCustomAttributes';
    const MAGENTO_MSI_IN_STORE_PICKUP_FRONTEND_MODULE_NAME = 'Magento_InventoryInStorePickupFrontend';
    const MAGENTO_GIFT_WRAPPING_MODULE_NAME = 'Magento_GiftWrapping';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Check if Klarna Kp module is enabled
     *
     * @return bool
     */
    public function isKlarnaKpEnabled()
    {
        return $this->moduleList->has(self::KLARNA_KP_MODULE_NAME);
    }

    /**
     * Check if Aheadworks Gift Card module enabled
     *
     * @return bool
     */
    public function isAheadworksGiftCardEnabled()
    {
        return $this->moduleList->has(self::AW_GIFTCARD_MODULE_NAME);
    }

    /**
     * Check if Magento Gift Card Account module enabled
     *
     * @return bool
     */
    public function isMagentoGiftCardEnabled()
    {
        return $this->moduleList->has(self::MAGENTO_GIFTCARD_MODULE_NAME);
    }

    /**
     * Check if Aheadworks customer attributes module enabled
     *
     * @return bool
     */
    public function isAheadworksCustomerAttributesEnabled()
    {
        return $this->moduleList->has(self::AW_CUSTOMER_ATTRIBUTES);
    }

    /**
     * Check if Magento customer attributes module enabled
     *
     * @return bool
     */
    public function isMagentoCustomerAttributesEnabled()
    {
        return $this->moduleList->has(self::MAGENTO_CUSTOMER_ATTRIBUTES);
    }

    /**
     * Check if Magento MSI In Store Pickup Frontend module is enabled
     *
     * @return bool
     */
    public function isMagentoMsiInStorePickupFrontendModuleEnabled()
    {
        return $this->moduleList->has(self::MAGENTO_MSI_IN_STORE_PICKUP_FRONTEND_MODULE_NAME);
    }

    /**
     * Check if Magento Gift Wrapping module is enabled
     *
     * @return bool
     */
    public function isMagentoGiftWrappingModuleEnabled()
    {
        return $this->moduleList->has(self::MAGENTO_GIFT_WRAPPING_MODULE_NAME);
    }
}
