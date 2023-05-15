<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\Payment;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config
{
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
     * Check if specific payment method is active
     *
     * @param string $paymentCode
     * @param int|null $storeId
     * @return bool
     */
    public function isMethodActive(string $paymentCode, ?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'payment/' . $paymentCode . '/active',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
