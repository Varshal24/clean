define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-list',
    'Magento_Checkout/js/model/quote'
], function (
    $,
    _,
    getTotalsAction,
    getPaymentInformationAction,
    paymentService,
    paymentMethodList,
    quote
) {
    'use strict';

    $.widget('mage.awOscSidebar', {
        options: {
            actualQuoteItems: quote.getItems(),
            offsetTop: 5 // todo: get this from margin-top property
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this._bind();
        },

        /**
         * Event binding
         */
        _bind: function () {
            $(document).on('ajax:removeFromCart', function (e, data) {
                this.unsetItem(data.productIds[0], data.item_id);

                if (this.options.actualQuoteItems.length) {
                    this.reloadPaymentInformation();
                    quote.setQuoteData(this.getDataIsVirtual())
                } else {
                    window.location.reload();
                }
            }.bind(this));

            $(document).on('ajax:updateCartItemQty', this.reloadPaymentInformation);
        },

        /**
         * Reload payment information
         */
        reloadPaymentInformation: function () {
            var deferred = $.Deferred();
            getTotalsAction([], deferred);
            getPaymentInformationAction(deferred);

            $.when(deferred).done(function () {
                paymentService.setPaymentMethods(paymentMethodList());
            });
        },

        /**
         * Get is virtual data for update quote data
         */
        getDataIsVirtual: function() {
            var virtualData = {
                is_virtual: 1
            };

            _.each(this.options.actualQuoteItems, function (data) {
                if (data.is_virtual === '0') {
                    virtualData.is_virtual = 0;
                }
            });

            return virtualData;
        },

        /**
         * Unset item from actual quote items
         */
        unsetItem: function(id, isUnsetByItemIdFlag = false) {
            var indexToDelete = null;

            _.each(this.options.actualQuoteItems, function (data, index) {
                if (data.item_id === id && isUnsetByItemIdFlag) {
                    indexToDelete = index;
                }

                if (data.product_id === id && !isUnsetByItemIdFlag) {
                    indexToDelete = index;
                }
            });

            if (indexToDelete !== null) {
                this.options.actualQuoteItems.splice(indexToDelete, 1)
            }
        },

        /**
         * Adjust element position
         */
        _adjust: function () {
            if ($(window).scrollTop() > this.options.offsetTop) {
                this.element.css({
                    'position': 'fixed',
                    'top': this.options.offsetTop
                });
            } else {
                this.element.css('position', 'static');
            }
        }
    });

    return $.mage.awOscSidebar;
});
