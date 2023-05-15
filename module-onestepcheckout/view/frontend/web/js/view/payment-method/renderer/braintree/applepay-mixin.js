define([
    'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-checkout-data'
], function (
    aggregateCheckoutData
) {
    'use strict';

    return function (renderer) {
        return renderer.extend({

            /**
             * Apple pay place order method
             */
            startPlaceOrder: function (nonce, event, session) {
                var self = this;

                aggregateCheckoutData.setCheckoutData().done(function () {
                    self.setPaymentMethodNonce(nonce);
                    self.placeOrder();
                    session.completePayment(ApplePaySession.STATUS_SUCCESS);
                });

                return this;
            },
        });
    }
});
