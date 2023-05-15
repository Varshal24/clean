<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ConfigProcessor
 */
class ConfigProcessor implements LayoutProcessorInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        ArrayManager $arrayManager
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     * @throws NoSuchEntityException
     */
    public function process($jsLayout)
    {
        $this->addShowPlaceOrderButton($jsLayout);
        $this->addAddressTypeToDisplayFirst($jsLayout);

        return $jsLayout;
    }

    /**
     * Add Show Place Order button
     *
     * @param array $jsLayout
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function addShowPlaceOrderButton(array &$jsLayout): void
    {
        $storeId = $this->storeManager->getStore()->getId();
        $isEnabledShowPlaceOrderButtonDefault = $this->config->isEnabledShowPlaceOrderButtonDefault($storeId);
        if ($isEnabledShowPlaceOrderButtonDefault) {
            $jsLayout['components']['checkout']['children']
            ['actions-toolbar']['config']['isRenderButtonDefault'] = $isEnabledShowPlaceOrderButtonDefault;
            $jsLayout['components']['checkout']['children']
            ['paymentMethod']['children']['methodList']
            ['config']['isRenderButtonDefault'] = $isEnabledShowPlaceOrderButtonDefault;

            $jsLayout = $this->arrayManager->move(
                'components/checkout/children/before-place-order/children/agreements',
                'components/checkout/children/paymentMethod/children/methodList/children/agreements',
                $jsLayout
            );
        }
    }

    /**
     * Add address type
     *
     * @param array $jsLayout
     * @return void
     */
    private function addAddressTypeToDisplayFirst(array &$jsLayout): void
    {
        $jsLayout['components']['checkout']['config']['addressTypeToDisplayFirst']
            = $this->config->getAddressTypeToDisplayFirst();
    }
}
