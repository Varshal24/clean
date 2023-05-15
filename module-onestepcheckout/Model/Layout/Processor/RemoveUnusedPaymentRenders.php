<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\OneStepCheckout\Model\Payment\Config as PaymentConfig;

class RemoveUnusedPaymentRenders implements LayoutProcessorInterface
{
    /**
     * @param ArrayManager $arrayManager
     * @param PaymentConfig $paymentConfig
     * @param array $paymentGroupsToCheck
     */
    public function __construct(
        private ArrayManager $arrayManager,
        private PaymentConfig $paymentConfig,
        private array $paymentGroupsToCheck = []
    ) {}

    /**
     * Remove unused payment renders
     *
     * @param array $jsLayout
     * @retrun array
     */
    public function process($jsLayout): array
    {
        $paymentRendersPath = 'components/checkout/children/checkoutConfig/children/payment-renders/children';
        $paymentRenders = $this->arrayManager->get($paymentRendersPath, $jsLayout);

        foreach ($paymentRenders as $groupKey => $groupParams) {
            $isNeedRemove = false;
            if ($this->isOnlyGroupNeedCheck($groupKey)) {
                $isNeedRemove = !$this->paymentConfig->isMethodActive($groupKey);
            } else if (count($groupParams['methods'])) {
                $disabledPaymentMethodsCount = 0;
                foreach ($groupParams['methods'] as $paymentCode => $paymentParams) {
                    if (!$this->paymentConfig->isMethodActive($paymentCode)) {
                        $disabledPaymentMethodsCount++;
                    }
                }

                $isNeedRemove = count($groupParams['methods']) === $disabledPaymentMethodsCount;
            }

            if ($isNeedRemove) {
                $jsLayout = $this->arrayManager->remove($paymentRendersPath . '/' . $groupKey, $jsLayout);
            }
        }

        return $jsLayout;
    }

    /**
     * Check if only payment group status should be checked
     *
     * @param string $groupKey
     * @return bool
     */
    private function isOnlyGroupNeedCheck(string $groupKey): bool
    {
        return in_array($groupKey, $this->paymentGroupsToCheck);
    }
}
