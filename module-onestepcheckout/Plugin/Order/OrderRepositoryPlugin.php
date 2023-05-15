<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    OneStepCheckout
 * @version    2.5.1
 * @copyright  Copyright (c) 2023 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Plugin\Order;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Aheadworks\OneStepCheckout\Model\Customer\Attribute\OrderAssigner as CustomerAttributeOrderAssigner;

/**
 * Plugin for \Magento\Sales\Api\OrderRepositoryInterface
 */
class OrderRepositoryPlugin
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var CustomerAttributeOrderAssigner
     */
    private $customerAttrOrderAssigner;

    /**
     * @param TimezoneInterface $timezone
     * @param OrderExtensionFactory $orderExtensionFactory
     * @param CustomerAttributeOrderAssigner $customerAttrOrderAssigner
     */
    public function __construct(
        TimezoneInterface $timezone,
        OrderExtensionFactory $orderExtensionFactory,
        CustomerAttributeOrderAssigner $customerAttrOrderAssigner
    ) {
        $this->timezone = $timezone;
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->customerAttrOrderAssigner = $customerAttrOrderAssigner;
    }

    /**
     * Add extension attributes to order
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $this->setExtensionAttributes($order);
        return $order;
    }

    /**
     * Add extension attributes to orders list
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, $searchResult)
    {
        foreach ($searchResult->getItems() as $order) {
            $this->setExtensionAttributes($order);
        }
        return $searchResult;
    }

    /**
     * Set extension attributes to order entity
     *
     * @param OrderInterface $order
     */
    private function setExtensionAttributes(OrderInterface $order)
    {
        /** @var OrderExtensionInterface $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }

        $deliveryDate = $order->getAwDeliveryDate();
        $deliveryDateFrom = $order->getAwDeliveryDateFrom();
        $deliveryDateTo = $order->getAwDeliveryDateTo();

        if ($deliveryDate) {
            $deliveryDate = $this->timezone->formatDateTime(
                $deliveryDate,
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::NONE
            );
        }
        if ($deliveryDateFrom && $deliveryDateTo) {
            $deliveryDateFrom = $this->timezone->formatDateTime(
                $deliveryDateFrom,
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::SHORT
            );
            $deliveryDateTo = $this->timezone->formatDateTime(
                $deliveryDateTo,
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::SHORT
            );
        }

        $extensionAttributes->setAwDeliveryDate($deliveryDate);
        $extensionAttributes->setAwDeliveryDateFrom($deliveryDateFrom);
        $extensionAttributes->setAwDeliveryDateTo($deliveryDateTo);
        $extensionAttributes->setAwOrderNote($order->getAwOrderNote());

        $this->customerAttrOrderAssigner->assign($order);

        $order->setExtensionAttributes($extensionAttributes);
    }
}
