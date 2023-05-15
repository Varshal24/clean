define([
        'ko',
        'underscore',
        'Magento_Checkout/js/model/quote',
        'moment',
    ], function (ko, _, quote, moment) {
        'use strict';
        var unavailableDates = {},
            dateRestrictionsConfig = window.checkoutConfig.deliveryDate.dateRestrictions,
            dayIncrement,
            dateToCheck,
            daysToAdd;

        if (dateRestrictionsConfig.estimatedDeliveryDate.length) {
            dateRestrictionsConfig.estimatedDeliveryDate.forEach(function(params) {
                if (unavailableDates[params.shipping_method] === undefined) {
                    unavailableDates[params.shipping_method] = {};
                }

                dayIncrement = 0;
                daysToAdd = params.number_of_days;
                while (0 < daysToAdd) {
                    dateToCheck = moment().add(dayIncrement, 'days').startOf('day');

                    if (dateRestrictionsConfig.weekdays.length) {
                        if (dateRestrictionsConfig.weekdays.includes(dateToCheck.day().toString())) {
                            unavailableDates[params.shipping_method][dayIncrement] = dateToCheck.toDate().getTime();
                            daysToAdd--;
                        }
                    } else {
                        unavailableDates[params.shipping_method][dayIncrement] = dateToCheck.toDate().getTime();
                        daysToAdd--;
                    }
                    dayIncrement++;
                }
            }, this);
        }

        /**
         * Is Date availabole
         *
         * @param {Date} date
         */
        return function (date) {
            var isAvailable = true,
                shippingMethod;

            if (quote.shippingMethod() != null) {
                shippingMethod = quote.shippingMethod().carrier_code + '_' + quote.shippingMethod().method_code;

                if (unavailableDates[shippingMethod]) {
                    date = moment(date).startOf('day').toDate();
                    if (_.contains(unavailableDates[shippingMethod], date.getTime())) {
                        isAvailable = false;
                    }
                }
            }

            return isAvailable;
        };
    }
);
