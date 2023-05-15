define(
    [
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/customer-data'
    ],
    function (ko, quote, customerData) {
        'use strict';

        if (window.giftOptionsConfig.hasOwnProperty('giftWrapping')) {
            quote.getTotals().subscribe(function () {
                customerData.reload(["aw-osc-gift-wrapping-info"]);
            }, this);
        }

        return {
            items: {
                'orderLevel' : [],
                'itemLevel' : []
            },

            /**
             * Add option to stored data
             *
             * @param {Integer|String} level
             * @param {Object} option
             */
            addOption: function (level, option) {
                if (level == 'orderLevel' || level == 'order') {
                    this.items.orderLevel = option;
                } else {
                    this.items.itemLevel[level] = option;
                }
            },

            /**
             * Get option from stored data
             *
             * @param {Integer|String} level
             * @return {Boolean|Object}
             */
            getOptionByLevel: function (level) {
                if (level == 'orderLevel' || level == 'order') {
                    return this.items.orderLevel;
                } else {
                    if (this.items.itemLevel.hasOwnProperty(level)) {
                        return this.items.itemLevel[level];
                    }
                }

                return false;
            }
        };
    });
