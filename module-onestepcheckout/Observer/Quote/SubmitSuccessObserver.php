<?php
namespace Aheadworks\OneStepCheckout\Observer\Quote;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Aheadworks\OneStepCheckout\Model\Customer\CustomerInfo\CustomerAssigner;
use Aheadworks\OneStepCheckout\Model\Config;

/**
 * On success quote submit observer
 */
class SubmitSuccessObserver implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerAssigner
     */
    private $customerAssigner;

    /**
     * @param Config $config
     * @param CustomerAssigner $customerAssigner
     */
    public function __construct(
        Config $config,
        CustomerAssigner $customerAssigner
    ) {
        $this->config = $config;
        $this->customerAssigner = $customerAssigner;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getOrder();
        if ($this->config->isCustomerInfoSectionDisplayed($order->getStoreId())
            && $order->getCustomerId()
            && $order->getAwOscCustomerInfo()
        ) {
            $this->customerAssigner->assignFromOrder($order);
        }

        return $this;
    }
}
