define(
    [
        'jquery',
        'mage/validation'
    ],
    function ($) {
        'use strict';

        var checkoutConfig = window.checkoutConfig,
            agreementsConfig = checkoutConfig ? checkoutConfig.checkoutAgreements : {},
            isEnabledDefaultPlaceOrderButton = checkoutConfig
                ? checkoutConfig.isEnabledDefaultPlaceOrderButton
                : false;

        return {
            paymentSectionInput: '.payment-method._active [data-role=checkout-agreements-input]',
            sidebarSectionInput: '.aw-sidebar-before-place-order [data-role=checkout-agreements-input]',

            /**
             * Validate checkout agreements
             *
             * @returns {boolean}
             */
            validate: function() {
                var inputSelector = isEnabledDefaultPlaceOrderButton
                        ? this.paymentSectionInput
                        : this.sidebarSectionInput,
                    input = $(inputSelector),
                    failureFound = false;

                if (agreementsConfig.isEnabled && input.length > 0) {
                    input.each(function (index, element) {
                        if (!$.validator.validateSingleElement(element, {
                            errorElement: 'div',
                            hideError: false
                        })) {
                            failureFound = true;
                        }
                    });

                    return !failureFound;
                }

                return true;
            }
        }
    }
);
