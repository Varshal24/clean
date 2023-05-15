define(
    [
        'ko',
        'Aheadworks_OneStepCheckout/js/view/address-form-visibility-service'
    ],
    function (ko, addressFormVisibilityService) {
        'use strict';

        var isDisabled = ko.computed(function () {
                return addressFormVisibilityService.isShippingAddressFormVisible() || addressFormVisibilityService.isBillingAddressFormVisible();
            });
        return {
            isDisabled: isDisabled
        };
    }
);
