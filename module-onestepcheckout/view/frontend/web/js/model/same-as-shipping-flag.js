define(
    [
        'ko',
        'Aheadworks_OneStepCheckout/js/model/checkout-data'
    ],
    function (ko, oscCheckoutData) {
        'use strict';

        if (oscCheckoutData.getAddressTypeToDisplayFirst() == 'billing') {
            oscCheckoutData.setSameAsShippingFlag(false);
        } else {
            oscCheckoutData.setSameAsBillingFlag(false);
        }

        var shippingFlag = ko.observable(oscCheckoutData.getSameAsShippingFlag());
        shippingFlag.subscribe(function (newValue) {
            oscCheckoutData.setSameAsShippingFlag(newValue);
        });

        var billingFlag = ko.observable(oscCheckoutData.getSameAsBillingFlag());
        billingFlag.subscribe(function (newValue) {
            oscCheckoutData.setSameAsBillingFlag(newValue);
        });

        return {
            sameAsShipping: shippingFlag,
            sameAsBilling: billingFlag
        };
    }
);
