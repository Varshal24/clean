<?php
namespace Aheadworks\OneStepCheckout\Model\Shipping;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * In store pickup shipping method carrier code value
     */
    const INVENTORY_IN_STORE_PICKUP_CARRIER_CODE = 'instore';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if specific shipping method is active
     *
     * @param string $carrierCode
     * @param int|null $storeId
     * @return bool
     */
    public function isMethodActive($carrierCode, $storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            'carriers/' . $carrierCode . '/active',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
