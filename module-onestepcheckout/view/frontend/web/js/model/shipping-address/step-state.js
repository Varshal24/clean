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

        var isVisibleFlag = ko.computed(function () {
                return !(
                    quote.isQuoteVirtual()
                    || instorePickupState.isStorePickupSelected()
                );
            })
        ;

        return {
            isVisible: isVisibleFlag
        };
    }
);
