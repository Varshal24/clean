define(
    ['ko', 'Magento_Checkout/js/model/quote'],
    function (ko, quote) {
        'use strict';

        var code = ko.observable(''),
            isApplied = ko.observable(false),
            totals = quote.getTotals();

        if (totals()) {
            code(totals()['coupon_code']);
        }

        totals.subscribe(function (totals) {
            if (!totals['coupon_code']) {
                isApplied(false);
            }
        });

        isApplied.subscribe(function (isApplied) {
            if (!isApplied) {
                code('');
            }
        });

        return {
            code: code,
            isApplied: isApplied
        }
    }
);
