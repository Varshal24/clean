<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Inventory\InStorePickup;

use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\ModuleManager as ThirdPartyModuleManager;
use Aheadworks\OneStepCheckout\Model\BaseFactory;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;

class FrontendLayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var ThirdPartyModuleManager
     */
    private $thirdPartyModuleManager;

    /**
     * @var BaseFactory
     */
    private $inventoryInStorePickupFrontendLayoutProcessorFactory;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ThirdPartyModuleManager $thirdPartyModuleManager
     * @param BaseFactory $inventoryInStorePickupFrontendLayoutProcessorFactory
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ThirdPartyModuleManager $thirdPartyModuleManager,
        BaseFactory $inventoryInStorePickupFrontendLayoutProcessorFactory,
        ArrayManager $arrayManager
    ) {
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
        $this->inventoryInStorePickupFrontendLayoutProcessorFactory =
            $inventoryInStorePickupFrontendLayoutProcessorFactory;
        $this->arrayManager = $arrayManager;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout)
    {
        if ($this->thirdPartyModuleManager->isMagentoMsiInStorePickupFrontendModuleEnabled()) {
            /** @var LayoutProcessorInterface|null $inventoryInStorePickupFrontendLayoutProcessor */
            $inventoryInStorePickupFrontendLayoutProcessor = $this
                ->inventoryInStorePickupFrontendLayoutProcessorFactory
                ->create()
            ;
            if ($inventoryInStorePickupFrontendLayoutProcessor) {
                return $inventoryInStorePickupFrontendLayoutProcessor->process($jsLayout);
            }
        }

        return $this->removeInventoryInStorePickupFrontendComponents($jsLayout);
    }

    /**
     * Remove inventory in store pickup ui components from layout.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    private function removeInventoryInStorePickupFrontendComponents(array $jsLayout)
    {
        $storePickupPath = $this->arrayManager->findPath('store-pickup', $jsLayout);

        return $this->arrayManager->remove($storePickupPath, $jsLayout);
    }
}
