<?php
namespace Aheadworks\OneStepCheckout\Model\Address\Attribute;

use Magento\Quote\Api\Data\AddressInterface;

/**
 * Class CustomAttributesFormatter
 *
 * @package Aheadworks\OneStepCheckout\Model\Address\Attribute
 */
class CustomAttributesFormatter
{
    /**
     * Format address custom attributes
     *
     * @param AddressInterface $address
     */
    public function format(AddressInterface $address)
    {
        $customAttributes = $address->getCustomAttributes();
        if ($customAttributes) {
            foreach ($customAttributes as $attribute) {
                $attributeValue = $attribute->getValue();
                if ($attributeValue && is_array($attributeValue)) {
                    if (isset($attributeValue['value']) && $attributeValue['value'] !== null) {
                        $attribute->setValue($attributeValue['value']);
                    }
                }
            }
        }
    }
}
