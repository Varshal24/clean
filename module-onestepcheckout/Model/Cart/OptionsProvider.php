<?php
namespace Aheadworks\OneStepCheckout\Model\Cart;

use Aheadworks\OneStepCheckout\Model\Product\ConfigurationPool;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Swatches\Block\Product\Renderer\ConfigurableFactory as SwatchRendererFactory;

/**
 * Class OptionsProvider
 * @package Aheadworks\OneStepCheckout\Model\Cart
 */
class OptionsProvider
{
    /**
     * @var CartItemRepositoryInterface
     */
    private $quoteItemRepository;

    /**
     * @var ConfigurationPool
     */
    private $configurationPool;

    /**
     * @var SwatchRendererFactory
     */
    private $swatchRendererFactory;

    /**
     * @param CartItemRepositoryInterface $quoteItemRepository
     * @param ConfigurationPool $configurationPool
     * @param SwatchRendererFactory $swatchRenderer
     */
    public function __construct(
        CartItemRepositoryInterface $quoteItemRepository,
        ConfigurationPool $configurationPool,
        SwatchRendererFactory $swatchRendererFactory
    ) {
        $this->quoteItemRepository = $quoteItemRepository;
        $this->configurationPool = $configurationPool;
        $this->swatchRendererFactory = $swatchRendererFactory;
    }

    /**
     * Get editable cart item options
     *
     * @param int $cartId
     * @return array
     */
    public function getOptionsData($cartId)
    {
        $optionsData = [];
        /** @var CartItemInterface|Item $item */
        foreach ($this->quoteItemRepository->getList($cartId) as $item) {
            $productType = $item->getProductType();
            if ($this->configurationPool->hasConfiguration($productType)) {
                $configuration = $this->configurationPool->getConfiguration($productType);
                $swatchRenderer = $this->swatchRendererFactory->create();
                $swatchRenderer->setProduct($item->getProduct());

                $optionsData[$item->getItemId()] = [
                    'product_type' => $productType,
                    'options' => $configuration->getOptions($item),
                    'jsonConfig' => $swatchRenderer->getJsonConfig(),
                    'jsonSwatchConfig' => $swatchRenderer->getJsonSwatchConfig()
                ];
            }
        }
        return $optionsData;
    }
}
