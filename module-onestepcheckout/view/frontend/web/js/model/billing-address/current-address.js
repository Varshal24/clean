define(
    [
        'ko',
        'Magento_Checkout/js/model/quote'
    ],
    function(ko, quote) {
        'use strict';

        var address = ko.observable(null),
            isValid = ko.observable(null),
            isNew = ko.observable(null);

        return {
            address: address,
            isValid: isValid,
            isNew: isNew,
            previousAddress: null,
            getPreviousAddress: function () {
               return this.previousAddress || quote.billingAddress();
            }
        };
    }
);
