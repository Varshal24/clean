<?php
namespace Aheadworks\OneStepCheckout\Model\Customer\Attribute;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Aheadworks\OneStepCheckout\Model\Customer\CustomerInfo\Converter as CustomerInfoConverter;

/**
 * Assign customer attributes from customer information data to order
 */
class OrderAssigner
{
    /**
     * @var CustomerInfoConverter
     */
    private $customerInfoConverter;

    /**
     * @param CustomerInfoConverter $customerInfoConverter
     */
    public function __construct(
        CustomerInfoConverter $customerInfoConverter
    ) {
        $this->customerInfoConverter = $customerInfoConverter;
    }

    /**
     * Assign static customer attributes e.g. dob, gender to guest users
     *
     * @param OrderInterface|Order $order
     */
    public function assign($order)
    {
        $customerInfoSerialized = $order->getAwOscCustomerInfo();
        if (!$order->getCustomerId() && $customerInfoSerialized) {
            $customerInfoArray = $this->customerInfoConverter->toArrayFromSerializedString(
                $customerInfoSerialized
            );
            if (!empty($customerInfoArray)) {
                unset($customerInfoArray['custom_attributes']);
                foreach ($customerInfoArray as $code => $value) {
                    $order->setData('customer_' . $code, $value);
                }
            }
        }
    }
}
