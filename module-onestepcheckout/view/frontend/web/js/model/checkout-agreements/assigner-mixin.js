define([
    'jquery',
    'underscore',
    'mage/utils/wrapper'
], function ($, _, wrapper) {
    'use strict';

    var agreementsConfig = window.checkoutConfig.checkoutAgreements,
        isEnabledDefaultPlaceOrderButton = checkoutConfig
            ? checkoutConfig.isEnabledDefaultPlaceOrderButton
            : false;

    return function (agreementsAssigner) {

        return wrapper.wrap(agreementsAssigner, function (originalAssigner, paymentData) {
            var paymentSectionInput = '.payment-method._active [data-role=checkout-agreements-input]',
                sidebarSectionInput = '.aw-sidebar-before-place-order [data-role=checkout-agreements-input]',
                agreementsInput = isEnabledDefaultPlaceOrderButton
                    ? paymentSectionInput
                    : sidebarSectionInput,
                agreementIds = [];

            if (agreementsConfig.isEnabled) {
                _.each($(agreementsInput).serializeArray(), function (item) {
                    agreementIds.push(item.value);
                });
                if (paymentData['extension_attributes'] === undefined) {
                    paymentData['extension_attributes'] = {};
                }
                paymentData['extension_attributes']['agreement_ids'] = agreementIds;
            }
        });
    };
});
