define([
    'underscore',
    'mage/utils/wrapper',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'Aheadworks_OneStepCheckout/js/model/address-form-state',
    'Magento_Customer/js/model/customer'
], function (_, wrapper, addressList, quote, addressFormState, customer) {
    'use strict';

    return function (selectShippingAddressAction) {
        return wrapper.wrap(selectShippingAddressAction, function (originalAction, shippingAddress) {
            var oldAddress = quote.shippingAddress();

            if (!_.has(shippingAddress, 'customAttributes')) {
                _.each(addressList(), function (address) {
                    if (_.has(address, 'customAttributes') && shippingAddress.getKey() === address.getKey()) {
                        shippingAddress.customAttributes = address.customAttributes;
                    }
                });
            }

            if (shippingAddress.saveInAddressBook === undefined && addressFormState.isShippingNewFormOpened()) {
                shippingAddress.saveInAddressBook = 1;
            }

            if (!customer.isLoggedIn()) {
                shippingAddress.saveInAddressBook = 1;
            }

            if (JSON.stringify(oldAddress) !== JSON.stringify(shippingAddress)) {
                return originalAction(shippingAddress);
            }
        });
    };
});
