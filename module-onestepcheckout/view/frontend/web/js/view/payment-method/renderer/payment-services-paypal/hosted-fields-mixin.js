define([
    'Aheadworks_OneStepCheckout/js/model/render-postprocessor',
],
function (postprocessor) {
    'use strict';

    return function (renderer) {
        return renderer.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/payment-method/renderer/payment-services-paypal/hosted-fields'
            },

            /**
             * Place Order Button Visible
             */
            isPlaceOrderButtonVisible: function () {
                return postprocessor.isRenderButtonDefault();
            }
        });
    }
});
