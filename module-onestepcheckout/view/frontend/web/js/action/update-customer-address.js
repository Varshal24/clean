define([
    'mage/storage',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'Aheadworks_OneStepCheckout/js/model/section-loader-service',
    'Magento_Checkout/js/model/error-processor'
], function (
    storage,
    urlBuilder,
    customer,
    sectionLoaderService,
    errorProcessor
) {
    'use strict';

    return function (address) {
        var serviceUrl = urlBuilder.createUrl('/awOsc/address/mine', {});

        sectionLoaderService.startLoading(['address-list', 'place-order-button']);

        return storage.post(
            serviceUrl,
            JSON.stringify({
                'address': address,
                'customerId': customer.customerData.id
            })
        ).fail(function (response) {
            errorProcessor.process(response);
        }).always(function () {
            sectionLoaderService.stopLoading(['address-list', 'place-order-button']);
        });
    };
});
