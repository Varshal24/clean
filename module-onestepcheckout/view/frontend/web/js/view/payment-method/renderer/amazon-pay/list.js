define([
    'Magento_Ui/js/lib/view/utils/async',
    'underscore',
    'ko',
    'Aheadworks_OneStepCheckout/js/view/payment-method/list',
    'Aheadworks_OneStepCheckout/js/model/render-postprocessor',
    'Amazon_Pay/js/model/storage'
], function (
    $,
    _,
    ko,
    Component,
    postProcessor,
    amazonStorage
) {
    'use strict';

    return Component.extend({
        paymentMethodItemsSelectors: '[data-role=payment-methods-load] div.payment-method',
        amazonPayment: 'amazon_payment_v2',

        /**
         * @inheritDoc
         */
        onRender: function () {
            var self = this;

            if (amazonStorage.isAmazonCheckout()) {
                $.async(this.paymentMethodItemsSelectors, function (methodItem) {
                    self._processPaymentsMethods($(methodItem));
                });
            } else {
                postProcessor.initProcessing();
            }
        },

        /**
         * Process payments methods if we logged in with Amazon
         *
         * @param {jQuery} paymentMethod
         */
        _processPaymentsMethods: function (paymentMethod) {
            var methodCode = postProcessor._getPaymentMethodCode(paymentMethod);

            if (methodCode != this.amazonPayment) {
                paymentMethod.hide();
            } else {
                this.hidePlaceOrderButton(paymentMethod);
            }
        },

        /**
         * hide place order button
         */
        hidePlaceOrderButton: function (paymentMethod) {
            if (!amazonStorage.isAmazonCheckout()) {
                postProcessor._hideActionToolbar(paymentMethod);
            } else if (!this.isRenderButtonDefault) {
                postProcessor._hideActionToolbar(paymentMethod);
            }
        }
    })
});
