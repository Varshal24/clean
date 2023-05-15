<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Magento\GiftWrapping\ConfigProviderFactory;
use Magento\GiftWrapping\Model\ConfigProvider;

/**
 * Class GiftWrappingInfo
 */
class GiftWrappingInfo implements SectionSourceInterface
{
    /**
     * @var ConfigProviderFactory
     */
    private $configProviderFactory;

    /**
     * @param ConfigProviderFactory $configProviderFactory
     */
    public function __construct(
        ConfigProviderFactory $configProviderFactory
    ) {
        $this->configProviderFactory = $configProviderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData(): array
    {
        $configProvider = $this->getConfigProvider();
        $data = [];
        if ($configProvider) {
            $data = [
                'designsInfo' => $configProvider->getDesignsInfo(),
                'appliedWrapping' => $configProvider->getAppliedWrapping(),
                'appliedPrintedCard' => $configProvider->getQuote()->getGwAddCard(),
                'appliedGiftReceipt' => $configProvider->getQuote()->getGwAllowGiftReceipt(),
                'cardInfo' => $configProvider->getCardInfo()
            ];
        }

        return $data;
    }

    /**
     * Retrieve Gift Wrapping Config Provider Class
     *
     * @return ConfigProvider|null
     */
    private function getConfigProvider(): ?ConfigProvider
    {
        return $this->configProviderFactory->create();
    }
}