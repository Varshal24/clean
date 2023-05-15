define(
    ['ko'],
    function (ko) {
        'use strict';

        var isShippingAddressFormVisible = ko.observable(false),
            isBillingAddressFormVisible = ko.observable(false);

        return {
            isShippingAddressFormVisible: isShippingAddressFormVisible,
            isBillingAddressFormVisible: isBillingAddressFormVisible
        };
    }
);
