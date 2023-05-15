define(
    [
        'underscore',
        'Aheadworks_OneStepCheckout/js/model/totals-service',
        'Aheadworks_OneStepCheckout/js/model/payment-methods-service',
        'Aheadworks_OneStepCheckout/js/model/cart-items-service',
        'Magento_Checkout/js/model/shipping-service',
        'Aheadworks_OneStepCheckout/js/model/place-order-button-service',
        'Aheadworks_OneStepCheckout/js/model/gift-message-service',
        'Aheadworks_OneStepCheckout/js/model/address-list-service'
    ],
    function (
        _,
        totalsService,
        paymentMethodsService,
        cartItemsService,
        shippingService,
        placeOrderButtonService,
        giftMessageService,
        addressListService
    ) {
        'use strict';

        /**
         * Get sections
         *
         * @return {Object}
         */
        function getSections () {
            return {
                'totals': totalsService.isLoading,
                'payment-methods': paymentMethodsService.isLoading,
                'cart-items': cartItemsService.isLoading,
                'shipping': shippingService.isLoading,
                'place-order-button': placeOrderButtonService.isLoading,
                'gift-message': giftMessageService.isLoading,
                'address-list': addressListService.isLoading
            }
        }

        /**
         * Change state
         *
         * @param {Array} sectionsToApply
         * @param {Boolean} state
         */
        function changeState(sectionsToApply, state) {
            var allSections = getSections();

            _.each(sectionsToApply, function (sectionToApply) {
                if (allSections.hasOwnProperty(sectionToApply)) {
                    allSections[sectionToApply](state);
                }
            });
        }

        return {

            /**
             * Start loading
             *
             * @param {Array} sectionsToApply
             * @returns {Boolean}
             */
            startLoading: function (sectionsToApply) {
                changeState(sectionsToApply, true);
            },

            /**
             * Stop loading
             *
             * @param {Array} sectionsToApply
             * @returns {Boolean}
             */
            stopLoading: function (sectionsToApply) {
                changeState(sectionsToApply, false);
            }
        };
    }
);
