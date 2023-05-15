define(
    [
        'jquery',
        'underscore',
        'uiRegistry',
        'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-validator',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry'
    ],
    function (
        $,
        _,
        registry,
        aggregateValidator,
        checkoutData,
        uiRegistry
    ) {
        'use strict';

        return {

            /**
             * Perform overall checkout data validation
             *
             * @returns {Boolean}
             */
            validate: function () {
                var selectedPaymentMethod = checkoutData.getSelectedPaymentMethod(),
                    isNeedValidate = true,
                    billingAddress = uiRegistry.get('index = billingAddress');

                if (selectedPaymentMethod && billingAddress) {
                    isNeedValidate = billingAddress._isAvailableForMethod(selectedPaymentMethod);
                }

                return isNeedValidate ? aggregateValidator.groupValidateMethods(true) : !isNeedValidate;
            }
        };
    }
);
