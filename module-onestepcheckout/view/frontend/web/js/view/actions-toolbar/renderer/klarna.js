define([
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default',
    'Klarna_Kp/js/model/klarna',
    'Klarna_Kp/js/model/config',
    'Magento_Checkout/js/model/payment/additional-validators'
], function (Component, klarna, config, additionalValidators) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/actions-toolbar/renderer/klarna'
        },
        getCategoryId: function () {
            // Strip off "klarna_"
            return this.methodCode.substr(7);
        },

        hasMessage: function () {
            return config.message !== null || config.client_token === null || config.client_token === '';
        },

        authorize: function () {
            var self = this;

            if (additionalValidators.validate()) {
                if (this.hasMessage()) {
                    return;
                }
                klarna.authorize(self.getCategoryId(), klarna.getUpdateData(), function (res) {
                    if (res.approved) {
                        if (res.finalize_required) {
                            self.finalize();
                            return;
                        }
                        self.placeOrder();
                    }
                });
            }
        },
        finalize: function () {
            var self = this;

            if (this.hasMessage()) {
                self.showButton(false);
                return;
            }
            klarna.finalize(self.getCategoryId(), klarna.getUpdateData(), function (res) {
                if (res.approved) {
                    self.placeOrder();
                }
            });
        },
    });
});
