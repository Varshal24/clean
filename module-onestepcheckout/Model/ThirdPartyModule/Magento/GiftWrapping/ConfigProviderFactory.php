<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Magento\GiftWrapping;

use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\ModuleManager;
use Magento\GiftWrapping\Model\ConfigProvider;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class ConfigProviderFactory
 */
class ConfigProviderFactory
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ModuleManager $moduleManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ModuleManager $moduleManager,
        ObjectManagerInterface $objectManager
    ) {
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
    }

    /**
     * Create Gift Wrapping Config Provider class
     *
     * @return ConfigProvider|null
     */
    public function create(): ?ConfigProvider
    {
        return $this->moduleManager->isMagentoGiftWrappingModuleEnabled()
            ? $this->objectManager->create(ConfigProvider::class)
            : null;
    }
}
