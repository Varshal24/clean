define([
    'ko'
], function (ko) {
    'use strict';

    let isBillingFormOpened = ko.observable(false),
        isBillingNewFormOpened = ko.observable(false), //compatibility with old billing new form state
        isShippingFormOpened = ko.observable(false),
        isShippingNewFormOpened = ko.observable(false) //compatibility with old shipping new form state

    return {
        isBillingFormOpened: isBillingFormOpened,
        isBillingNewFormOpened: isBillingNewFormOpened,
        isShippingFormOpened: isShippingFormOpened,
        isShippingNewFormOpened: isShippingNewFormOpened
    };
});
