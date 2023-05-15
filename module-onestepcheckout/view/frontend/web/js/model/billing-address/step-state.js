define(
    [
        'ko',
        'Magento_Checkout/js/model/quote',
        'Aheadworks_OneStepCheckout/js/model/inventory/instore-pickup/state'
    ],
    function(
        ko,
        quote,
        instorePickupState
    ) {
        'use strict';

        var canUseShippingAddressFlag = ko.computed(function () {
                if (quote.isQuoteVirtual()) {
                    return false;
                }

                if (!quote.billingAddress()) {
                    return true;
                }

                return quote.billingAddress().canUseForBilling()
                    && !instorePickupState.isStorePickupSelected();
            })
        ;

        return {
            canUseShippingAddress: canUseShippingAddressFlag
        };
    }
);
