define([], function () {
    'use strict';

    return function (renderer) {
        return renderer.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/payment-method/renderer/payment-services-paypal/vault'
            },
        });
    }
});