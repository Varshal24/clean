define([
    'jquery',
    'underscore',
    'Aheadworks_OneStepCheckout/js/view/animation',
    'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-validator',
    'uiRegistry',
    'awOscValidationMock'
], function ($, _, animation, aggregateValidator, uiRegistry) {
    'use strict';

    return function (checkoutExpress) {

        return checkoutExpress.extend({
            paypalLoadCheck: false,
            fieldSelector: '.aw-onestep-main input, .checkout-agreements input, .aw-onestep-main select, .aw-onestep-main textarea',
            /**
             * @inheritdoc
             */
            initListeners: function() {
                $(this.fieldSelector).on('change', function () {
                    if ($('#co-payment-form #paypal_express').is(':checked')) {
                        this.validate();
                    }
                }.bind(this));

                this._super()
            },

            /**
             * @return {Boolean}
             */
            selectPaymentMethod: function () {
                var result = this._super();
                this.validateShippingAddress();

                return result;
            },

            /**
             * Validate shipping address
             */
            validateShippingAddress: function() {
                var shippingAddress = uiRegistry.get('index = shippingAddress');
                aggregateValidator.validateShippingAddress(true, true);
                shippingAddress.isAddressSameAsBilling(false);
                $(document).trigger('select-paypal-express-payment');
            },

            /**
             *  @inheritdoc
             */
            validate: function () {
                var validMock,
                    styleForError =
                        '<style>' +
                        '.onestepcheckout-index-index div.mage-error[generated] { display: none;} ' +
                        '.onestepcheckout-index-index ._error div.mage-error[generated] { display: block;}' +
                        '</style>';

                this._super();

                if (!this.paypalLoadCheck) {
                    this.paypalLoadCheck = true;
                    validMock = $(this.fieldSelector).awOscValidationMock();
                    validMock.trigger('awOscVMReset', [true]);
                    $('.aw-onestep-main').append(styleForError);
                    $(this.fieldSelector).parents('._error').each(function(){
                        $(this).removeClass('_error');
                    });
                }
            },

            /**
             *  @inheritdoc
             */
            addError: function () {
                this._super();
                animation.scrollToTop();
            }
        });
    }
});
