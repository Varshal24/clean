define([
    'jquery',
    'ko',
    'mage/translate',
    'Aheadworks_OneStepCheckout/js/view/sidebar/payment-option/gift-card-account',
    'Magento_GiftCardAccount/js/model/payment/gift-card-messages',
    'Magento_SalesRule/js/model/payment/discount-messages',
    'Aheadworks_OneStepCheckout/js/model/payment-option/message-processor',
    'Aheadworks_OneStepCheckout/js/model/payment-option/coupon',
    'Aheadworks_OneStepCheckout/js/model/payment-option/adapter/discount'
], function (
    $,
    ko,
    $t,
    Component,
    giftCardMessageContainer,
    salesRuleMessageContainer,
    messageProcessor,
    coupon,
    discountAdapter
) {
    'use strict';

    var codeToApply = ko.observable(null);

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/sidebar/payment-option/merged-ee-gc-discount',
            inputSelector: '#code_to_apply'
        },
        couponCode: coupon.code,
        codeToApply: codeToApply,
        isCouponCodeApplied: coupon.isApplied,

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            coupon.isApplied(this.couponCode() != null);
            if (this.couponCode() != null) {
                this.codeToApply(this.couponCode())
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super();

            this.isCouponCodeApplied.subscribe(function (isApplied) {
                if (!isApplied) {
                    this.codeToApply('');
                }
            }, this);

            return this;
        },

        /**
         * Init messages processing
         *
         * @returns {Component}
         */
        _initMessagesProcessing: function () {
            var self = this;

            giftCardMessageContainer.getErrorMessages().subscribe(function (message) {
                if (message.length) {
                    message.length = 0;
                    message.push($t('Your Gift Card or Coupon Code is invalid'));
                }

                messageProcessor.processError($(self.inputSelector), giftCardMessageContainer)
            });
            giftCardMessageContainer.getSuccessMessages().subscribe(function () {
                messageProcessor.processSuccess($(self.inputSelector), giftCardMessageContainer)
            });

            return this;
        },

        /**
         * On apply button click
         */
        onApplyClick: function() {
            var input = $(this.inputSelector);

            if (this.validate()) {
                discountAdapter.applyCode(this.codeToApply())
                    .done(function () {
                        messageProcessor.processSuccess(input, salesRuleMessageContainer)
                    }).fail(function () {
                    this.giftCartCode(this.codeToApply());
                    this.setGiftCard();
                }.bind(this));
            }
        },

        /**
         * On cancel button click
         */
        onCancelClick: function() {
            this.codeToApply('');
            discountAdapter.onCancelClick()
        },

        /**
         * Check form is valid
         *
         * @returns {Boolean}
         */
        validate: function () {
            var form = $(this.formSelector);

            messageProcessor.resetImmediate($(this.inputSelector));
            return form.validation() && form.validation('isValid');
        }
    });
});
