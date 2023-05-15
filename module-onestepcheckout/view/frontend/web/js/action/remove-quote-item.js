define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Aheadworks_OneStepCheckout/js/model/section-loader-service',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/payment-service',
        'Aheadworks_OneStepCheckout/js/action/redirect-on-empty-quote',
        'Magento_Checkout/js/model/shipping-service',
        'Aheadworks_OneStepCheckout/js/action/get-sections-details',
        'Aheadworks_OneStepCheckout/js/action/reload-checkout',
    ],
    function (
        $,
        quote,
        urlBuilder,
        storage,
        errorProcessor,
        sectionLoaderService,
        customer,
        methodConverter,
        paymentService,
        redirectOnEmptyQuoteAction,
        shippingService,
        getSectionsDetailsAction,
        reloadCheckoutAction
    ) {
        'use strict';

        return function (itemId) {
            var serviceUrl;

            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/awOsc/guest-carts/:cartId/cart-items/:itemId', {
                    cartId: quote.getQuoteId(),
                    itemId: itemId
                });
            } else {
                serviceUrl = urlBuilder.createUrl('/awOsc/carts/mine/cart-items/:itemId', {
                    itemId: itemId
                });
            }
            sectionLoaderService.startLoading(['totals', 'payment-methods', 'cart-items', 'place-order-button']);

            return storage.delete(
                serviceUrl
            ).done(
                function (response) {
                    var cartDetails = response.cart_details,
                        paymentDetails = response.payment_details;

                    if (cartDetails.items_count < 1) {
                        redirectOnEmptyQuoteAction.execute();
                    }

                    if (window.checkoutConfig.reloadAfterQuoteItemRemovalFlag) {
                        reloadCheckoutAction.execute();
                    }

                    $(document).trigger('ajax:removeFromCart', {
                        productIds: [String(itemId)],
                        item_id: String(itemId)
                    });

                    quote.setTotals(paymentDetails.totals);
                    quote.setQuoteData(cartDetails);
                    paymentService.setPaymentMethods(methodConverter(paymentDetails.payment_methods));

                    shippingService.isLoading(true);
                    getSectionsDetailsAction(['shippingMethods', 'totals']).always(function () {
                        shippingService.isLoading(false);
                    });
                }.bind(itemId)
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
