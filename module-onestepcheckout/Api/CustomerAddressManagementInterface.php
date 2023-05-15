<?php
namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface CustomerAddressManagementInterface
 * @package Aheadworks\OneStepCheckout\Api
 * @api
 */
interface CustomerAddressManagementInterface
{
    /**
     * Update customer address
     *
     * @param int $customerId
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @return \Magento\Customer\Api\Data\AddressInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateAddress(
        $customerId,
        \Magento\Quote\Api\Data\AddressInterface $address
    );
}
