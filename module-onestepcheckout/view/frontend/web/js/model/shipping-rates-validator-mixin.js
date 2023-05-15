define([
    'underscore',
    'Magento_Checkout/js/model/address-converter',
    'uiRegistry',
    'Magento_Checkout/js/action/select-shipping-address'
], function (
    _,
    addressConverter,
    uiRegistry,
    selectShippingAddress
) {
    'use strict';

    return function (validator) {
        return _.extend(validator, {

            /**
             * @inheritdoc
             */
            validateFields: function() {
                var addressFlat = addressConverter.formDataProviderToFlatData(
                    this.collectObservedData(),
                    'shippingAddress'
                    ),
                    address;

                if (this.validateAddressData(addressFlat)) {
                    addressFlat = uiRegistry.get('checkoutProvider').shippingAddress;
                    if (!addressFlat.customer_address_id) {
                        address = addressConverter.formAddressDataToQuoteAddress(addressFlat);
                        selectShippingAddress(address);
                    }
                }
            }
        });
    }
});
