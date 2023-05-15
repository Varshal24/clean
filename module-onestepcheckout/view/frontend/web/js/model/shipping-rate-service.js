define(
    [
        'ko',
        'Magento_Checkout/js/model/quote',
        'Aheadworks_OneStepCheckout/js/model/estimation-data-resolver',
        'Aheadworks_OneStepCheckout/js/action/get-sections-details',
        'Magento_Checkout/js/model/shipping-service',
        'Aheadworks_OneStepCheckout/js/model/payment-methods-service',
        'Aheadworks_OneStepCheckout/js/model/totals-service',
        'Aheadworks_OneStepCheckout/js/model/address-form-state'
    ],
    function (
        ko,
        quote,
        estimationDataResolver,
        getSectionsDetailsAction,
        shippingService,
        paymentMethodsService,
        totalsService,
        addressFormState
    ) {
        'use strict';

        quote.shippingAddress.subscribe(function () {
            var sections = ['totals'],
                isNeedToUpdatePaymentMethods = estimationDataResolver.resolveBillingAddress(),
                isNeedToUpdateShippingMethods = estimationDataResolver.resolveShippingAddress()
                    && !quote.isQuoteVirtual();

            if (!addressFormState.isShippingNewFormOpened()) {
                if (isNeedToUpdateShippingMethods) {
                    shippingService.isLoading(true);
                    sections.push('shippingMethods');
                }
                if (isNeedToUpdatePaymentMethods) {
                    paymentMethodsService.isLoading(true);
                    sections.push('paymentMethods');
                }
                totalsService.isLoading(true);

                getSectionsDetailsAction(sections, true).always(function () {
                    if (isNeedToUpdateShippingMethods) {
                        shippingService.isLoading(false);
                    }
                    if (isNeedToUpdatePaymentMethods) {
                        paymentMethodsService.isLoading(false);
                    }
                    totalsService.isLoading(false);
                });
            }
        });

        return {};
    }
);
