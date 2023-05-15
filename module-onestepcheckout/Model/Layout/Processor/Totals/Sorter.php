<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Totals;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Sorter
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private StoreManagerInterface $storeManager
    ) {
    }

    /**
     * Sort totals
     *
     * @param array $config
     * @return array
     * @throws NoSuchEntityException
     */
    public function sort(array $config): array
    {
        $storeId = $this->storeManager->getStore()->getId();
        $sortData = $this->scopeConfig->getValue(
            'sales/totals_sort',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        foreach ($config as $code => &$configData) {
            $sortTotalCode = str_replace('-', '_', $code);
            if (isset($sortData[$sortTotalCode]) && isset($config[$code])) {
                $configData['sortOrder'] = $sortData[$sortTotalCode];
            }
        }

        return $config;
    }
}
