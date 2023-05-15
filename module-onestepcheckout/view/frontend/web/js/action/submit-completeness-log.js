define(
    [
        'jquery',
        'Magento_Checkout/js/model/url-builder',
        'mage/url',
        'mage/storage',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Aheadworks_OneStepCheckout/js/model/completeness-logger/service-enable-flag'
    ],
    function (
        $,
        urlBuilder,
        mageUrlBuilder,
        storage,
        customer,
        quote,
        serviceEnableFlag
    ) {
        'use strict';

        return function (log) {
            var serviceUrl,
                payload = {fieldCompleteness: log};

            if (!serviceEnableFlag()) {
                return $.Deferred().resolve();
            }

            serviceUrl = urlBuilder.createUrl('/awOsc/carts/:cartId/completeness-log', {
                cartId: quote.getQuoteId()
            });

            if (navigator.sendBeacon) {
                var blob = new Blob([JSON.stringify(payload)], { type: "application/json" });
                navigator.sendBeacon(mageUrlBuilder.build(serviceUrl), blob);
            } else {
                return storage.post(serviceUrl, JSON.stringify(payload));
            }
        }
    }
);
