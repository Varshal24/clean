define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_SalesRule/js/action/set-coupon-code',
    'Magento_SalesRule/js/action/cancel-coupon',
    'Magento_SalesRule/js/model/payment/discount-messages',
    'Aheadworks_OneStepCheckout/js/model/payment-option/coupon',
    'Aheadworks_OneStepCheckout/js/model/payment-option/message-processor'
], function (
    $,
    quote,
    setCouponCodeAction,
    cancelCouponAction,
    messageContainer,
    coupon,
    messageProcessor
) {
    'use strict';

    var inputSelector = '#code_to_apply';

    return {
        applyCode: function(code) {
            coupon.code(code);
            return setCouponCodeAction(coupon.code(), coupon.isApplied);
        },

        /**
         * On cancel button click
         */
        onCancelClick: function() {
            coupon.code('');
            this._processMessages(cancelCouponAction(coupon.isApplied));
        },

        /**
         * Add process messages handlers
         *
         * @param {Deferred} deferred
         */
        _processMessages: function (deferred) {
            var input = $(inputSelector);

            deferred.done(function () {
                messageProcessor.processSuccess(input, messageContainer)
            }).fail(function () {
                messageProcessor.processError(input, messageContainer)
            });
        }
    };
});
