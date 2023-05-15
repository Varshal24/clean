define([
    'jquery',
    'ko',
    'mage/translate',
    'Aheadworks_Giftcard/js/view/payment/one-step/gift-card',
    'Aheadworks_Giftcard/js/model/payment/giftcard-messages',
    'Aheadworks_Giftcard/js/action/apply-giftcard-code',
    'Aheadworks_Giftcard/js/view/payment/one-step/apply-by-code-flag',
    'Aheadworks_OneStepCheckout/js/model/payment-option/coupon',
    'Aheadworks_OneStepCheckout/js/model/payment-option/message-processor',
    'Aheadworks_OneStepCheckout/js/model/payment-option/adapter/discount',
], function (
    $,
    ko,
    $t,
    Component,
    messageContainer,
    applyGiftCardCodeAction,
    applyByCodeFlag,
    coupon,
    messageProcessor,
    discountAdapter
) {
    'use strict';

    var codeToApply = ko.observable(null);

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/sidebar/payment-option/merged-aw-gc-discount',
            inputSelector: '#code_to_apply',
            formSelector: '#aw-merged-gc-discount-form'
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
         * On apply button click
         */
        onApplyClick: function() {
            var input = $(this.inputSelector);

            if (this.validate()) {
                discountAdapter.applyCode(this.codeToApply())
                    .done(function () {
                        messageProcessor.processSuccess(input, messageContainer)
                    }).fail(function () {
                        this.giftcardCode(this.codeToApply());
                        this.applyGiftCard();
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
         * Apply gift card
         */
        applyGiftCard: function() {
            var input = $(this.inputSelector);

            applyByCodeFlag(false);
            applyGiftCardCodeAction(this.giftcardCode())
                .done(function () {
                    this.codeToApply('');
                    messageProcessor.processSuccess(input, messageContainer)
                }.bind(this))
                .fail(function () {
                    messageContainer.addErrorMessage({
                        'message': $t('Your Gift Card or Coupon Code is invalid')
                    });
                    messageProcessor.processError(input, messageContainer)
                });
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
    })
});
