define(
    [
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/model/payment/method-list',
        'Aheadworks_OneStepCheckout/js/model/checkout-section/service-busy-flag',
        'Aheadworks_OneStepCheckout/js/model/shipping-information/service-busy-flag',
        'Aheadworks_OneStepCheckout/js/model/place-order-button-service',
        'Aheadworks_OneStepCheckout/js/model/place-order-button-disabled-flag'
    ],
    function (
        ko,
        quote,
        shippingService,
        paymentMethodList,
        sectionsServiceBusyFlag,
        shippingInfoServiceBusyFlag,
        placeOrderButtonService,
        placeOrderButtonDisabledFlag

    ) {
        'use strict';

        return ko.computed(function () {
            return paymentMethodList().length > 0
                && (!quote.isQuoteVirtual() && (shippingService.getShippingRates())().length > 0
                || (quote.isQuoteVirtual() && quote.billingAddress() !== null))
                && !sectionsServiceBusyFlag()
                && !shippingInfoServiceBusyFlag()
                && !placeOrderButtonService.isLoading()
                && !placeOrderButtonDisabledFlag.isDisabled()
        });
    }
);
