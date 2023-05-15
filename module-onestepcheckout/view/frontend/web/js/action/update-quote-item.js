define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/payment-service',
        'Aheadworks_OneStepCheckout/js/model/section-loader-service',
        'Aheadworks_OneStepCheckout/js/action/get-sections-details',
        'Magento_Customer/js/customer-data'
    ],
    function (
        quote,
        urlBuilder,
        storage,
        errorProcessor,
        customer,
        methodConverter,
        paymentService,
        sectionLoaderService,
        getSectionsDetailsAction,
        customerData
    ) {
        'use strict';

        return function (item) {
            var serviceUrl,
                payload = {
                    item: item
                };

            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/awOsc/guest-carts/:cartId/cart-items', {
                    cartId: quote.getQuoteId()
                });
            } else {
                serviceUrl = urlBuilder.createUrl('/awOsc/carts/mine/cart-items', {});
            }
            sectionLoaderService.startLoading(['totals', 'payment-methods', 'cart-items', 'place-order-button']);

            return storage.post(
                serviceUrl,
                JSON.stringify(payload)
            ).done(
                function (response) {
                    var cartDetails = response.cart_details,
                        paymentDetails = response.payment_details;

                    quote.setTotals(paymentDetails.totals);
                    quote.setQuoteData(cartDetails);
                    paymentService.setPaymentMethods(methodConverter(paymentDetails.payment_methods));

                    sectionLoaderService.startLoading(['shipping']);
                    customerData.invalidate(['cart']);
                    customerData.reload(['cart'], true);
                    getSectionsDetailsAction(['shippingMethods', 'totals']).always(function () {
                        sectionLoaderService.stopLoading(['shipping', 'place-order-button']);
                    });
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                }
            ).always(function () {
                sectionLoaderService.stopLoading(['totals', 'payment-methods', 'cart-items', 'place-order-button']);
            });
        };
    }
);
