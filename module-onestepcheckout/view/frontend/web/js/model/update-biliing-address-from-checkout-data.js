define([
    'Magento_Checkout/js/model/quote',
    'Aheadworks_OneStepCheckout/js/model/checkout-data',
    'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag',
    'Magento_Checkout/js/model/address-converter',
    'Aheadworks_OneStepCheckout/js/model/address-form-state'
], function (
    quote,
    checkoutData,
    sameAddressAsFlag,
    addressConverter,
    addressFromState
) {
    'use strict';

    return function () {
        if (sameAddressAsFlag.sameAsBilling() && quote.billingAddress() != null && addressFromState.isBillingFormOpened()) {
            var address = addressConverter.formAddressDataToQuoteAddress(checkoutData.getBillingAddressFromData());
            quote.billingAddress(address)
        }
    };
});
