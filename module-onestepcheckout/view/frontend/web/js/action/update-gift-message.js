define([
    'mage/storage',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'Aheadworks_OneStepCheckout/js/action/get-sections-details',
    'Aheadworks_OneStepCheckout/js/model/gift-message-service',
    'Aheadworks_OneStepCheckout/js/model/section-loader-service',
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/quote'
], function (
    storage,
    urlBuilder,
    customer,
    getSectionsDetailsAction,
    giftMessageService,
    sectionLoaderService,
    messageList,
    errorProcessor,
    quote
) {
    'use strict';

    return function (itemId, giftMessage) {
        var serviceUrl;

        if (customer.isLoggedIn()) {
            serviceUrl = urlBuilder.createUrl('/awOsc/carts/mine/gift-message', {});
            if (itemId !== 'order' && itemId !== 'orderLevel') {
                serviceUrl = urlBuilder.createUrl('/awOsc/carts/mine/gift-message/:itemId', {itemId: itemId});
            }
        } else {
            serviceUrl = urlBuilder.createUrl('/awOsc/guest-carts/:cartId/gift-message', {cartId: quote.getQuoteId()});
            if (itemId !== 'order' && itemId !== 'orderLevel') {
                serviceUrl = urlBuilder.createUrl(
                    '/awOsc/guest-carts/:cartId/gift-message/:itemId',
                    {cartId: quote.getQuoteId(), itemId: itemId}
                );
            }
        }
        messageList.clear();
        sectionLoaderService.startLoading(['gift-message', 'place-order-button', 'totals', 'cart-items']);

        return storage.post(
            serviceUrl,
            JSON.stringify({
                'gift_message': giftMessage
            })
        ).done(function () {
            getSectionsDetailsAction(['giftMessage', 'totals']).always(function () {
                sectionLoaderService.stopLoading(['gift-message', 'place-order-button', 'totals', 'cart-items']);
            });
        }).fail(function (response) {
            errorProcessor.process(response);
            sectionLoaderService.stopLoading(['gift-message', 'place-order-button', 'totals', 'cart-items']);
        });
    };
});
