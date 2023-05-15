<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\GiftWrapping;

use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\ModuleManager as ThirdPartyModuleManager;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;

class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var ThirdPartyModuleManager
     */
    private $thirdPartyModuleManager;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ThirdPartyModuleManager $thirdPartyModuleManager
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ThirdPartyModuleManager $thirdPartyModuleManager,
        ArrayManager $arrayManager
    ) {
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
        $this->arrayManager = $arrayManager;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout): array
    {
        if ($this->thirdPartyModuleManager->isMagentoGiftWrappingModuleEnabled()) {
            $pathToAdd = [
                'giftWrapping' => 'components/checkout/children/payment-options/children',
                'giftWrapperRendererConfig' => 'components/checkout/children/cart-items/children/details'
            ];

            foreach($pathToAdd as $name => $path) {
                $jsLayout = $this->arrayManager->merge(
                    $path,
                    $jsLayout,
                    [
                        $name => [
                            'component' => 'Aheadworks_OneStepCheckout/js/view/sidebar/gift-wrapping',
                            'sortOrder' => 20
                        ]
                    ]
                );
            }
        }

        return $jsLayout;
    }
}
