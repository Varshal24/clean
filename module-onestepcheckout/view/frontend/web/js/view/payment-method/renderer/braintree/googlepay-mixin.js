define([
    'underscore',
    'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-checkout-data'
], function (_, aggregateCheckoutData) {
    'use strict';

    return function (renderer) {
        return renderer.extend({

            /**
             * Google pay place order method
             */
            startPlaceOrder: function (nonce, paymentData, device_data) {
                var self = this;

                aggregateCheckoutData.setCheckoutData().done(function () {
                    self.setPaymentMethodNonce(nonce);
                    if (!_.isUndefined(self.setDeviceData)){
                        self.setDeviceData(device_data);
                    }
                    self.placeOrder();
                });

                return this;
            },
        });
    }
});
