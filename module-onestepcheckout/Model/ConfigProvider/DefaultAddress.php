<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\ConfigProvider;

use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Api\Data\AddressInterfaceFactory;

class DefaultAddress implements ConfigProviderInterface
{
    /**
     * @param CustomerSession $customerSession
     * @param Config $config
     * @param AddressInterfaceFactory $addressInterfaceFactory
     */
    public function __construct(
        private CustomerSession $customerSession,
        private Config $config,
        private AddressInterfaceFactory $addressInterfaceFactory
    ) {
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        $customer = $this->customerSession->getCustomer();
        $address = $this->addressInterfaceFactory->create();

        if (!$customer->getDefaultShippingAddress()
            && $countryId = $this->config->getDefaultCountryId()
        ) {
            $address->setCountryId($countryId);
            $config['shippingAddressFromData'] = $address->getData();
        }

        if (!$customer->getDefaultBillingAddress()
            && $countryId = $this->config->getDefaultCountryId()
        ) {
            $address->setCountryId($countryId);
            $config['billingAddressFromData'] = $address->getData();
        }

        return $config;
    }
}
