define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Aheadworks_OneStepCheckout/js/model/section-loader-service',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/payment-service',
        'Aheadworks_OneStepCheckout/js/model/editable-item-options',
        'Aheadworks_OneStepCheckout/js/model/image-data',
        'Aheadworks_OneStepCheckout/js/model/json-error-processor'
    ],
    function (
        $,
        quote,
        sectionLoaderService,
        customer,
        methodConverter,
        paymentService,
        editableItemOptions,
        imageData,
        jsonErrorProcessor
    ) {
        'use strict';

        var isAjaxSend = false,
            submitUrl = window.checkoutConfig.optionsPostUrl,
            formKey = $.mage.cookies.get('form_key');

        return function (itemId, options) {
            if (!isAjaxSend) {
                sectionLoaderService.startLoading(['totals', 'payment-methods', 'cart-items', 'place-order-button']);

                isAjaxSend = true;
                return $.ajax({
                    url: submitUrl,
                    type: 'POST',
                    data: {
                        itemId: itemId,
                        options: JSON.stringify(options),
                        form_key: formKey
                    },
                    global: true,
                    dataType: 'json'
                }).done(
                    function (response) {
                        var optionsDetails;

                        if (response.success) {
                            optionsDetails = response.optionDetails;

                            editableItemOptions.setConfigOptionsData(JSON.parse(optionsDetails.options_details));
                            imageData.setItemsImageData(JSON.parse(optionsDetails.image_details));
                            quote.setTotals(optionsDetails.payment_details.totals);
                            paymentService.setPaymentMethods(
                                methodConverter(optionsDetails.payment_details.payment_methods)
                            );
                        } else {
                            jsonErrorProcessor.process(response);
                        }
                    }
                ).always(function () {
                    sectionLoaderService.stopLoading(['totals', 'payment-methods', 'cart-items', 'place-order-button']);
                    isAjaxSend = false;
                });
            }
        };
    }
);
