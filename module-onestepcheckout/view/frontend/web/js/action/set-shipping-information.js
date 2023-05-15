define(
    [
        'underscore',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/action/select-billing-address',
        'Aheadworks_OneStepCheckout/js/model/shipping-information/service-busy-flag',
        'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag',
        'Aheadworks_OneStepCheckout/js/model/address-form-state',
        'Aheadworks_OneStepCheckout/js/model/checkout-data',
        'Magento_Checkout/js/model/address-converter'
    ],
    function (
        _,
        quote,
        resourceUrlManager,
        storage,
        errorProcessor,
        selectBillingAddressAction,
        serviceBusyFlag,
        sameAddressAsFlag,
        addressFromState,
        checkoutData,
        addressConverter
    ) {
        'use strict';
        return function () {
            var payload, shippingAddress, billingAddress;

            shippingAddress = addressFromState.isShippingFormOpened()
                ? addressConverter.formAddressDataToQuoteAddress(checkoutData.getShippingAddressFromData())
                : quote.shippingAddress();

            billingAddress = addressFromState.isBillingFormOpened()
                ? addressConverter.formAddressDataToQuoteAddress(checkoutData.getBillingAddressFromData())
                : quote.billingAddress();

            if (sameAddressAsFlag.sameAsShipping()) {
                billingAddress = shippingAddress;
                if (!quote.billingAddress() || !quote.isQuoteVirtual()) {
                    selectBillingAddressAction(billingAddress);
                }
            }

            if (sameAddressAsFlag.sameAsBilling()) {
                shippingAddress = billingAddress;
            }

            payload = {
                addressInformation: {
                    shipping_address: _.extend(
                        {},
                        shippingAddress,
                        {'same_as_billing': !quote.isQuoteVirtual() && sameAddressAsFlag.sameAsShipping() ? 1 : 0}
                    ),
                    billing_address: billingAddress,
                    shipping_method_code: quote.shippingMethod().method_code,
                    shipping_carrier_code: quote.shippingMethod().carrier_code
                }
            };

            serviceBusyFlag(true);

            return storage.post(
                resourceUrlManager.getUrlForSetShippingInformation(quote),
                JSON.stringify(payload)
            ).done(
                function () {
                    serviceBusyFlag(false);
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                }
            );
        }
    }
);
