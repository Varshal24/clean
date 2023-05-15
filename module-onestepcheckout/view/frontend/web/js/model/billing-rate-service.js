define(
    [
        'ko',
        'Magento_Checkout/js/model/quote',
        'Aheadworks_OneStepCheckout/js/model/estimation-data-resolver',
        'Aheadworks_OneStepCheckout/js/action/get-sections-details',
        'Magento_Checkout/js/model/shipping-service',
        'Aheadworks_OneStepCheckout/js/model/payment-methods-service',
        'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag',
        'Aheadworks_OneStepCheckout/js/model/address-form-state'
    ],
    function (
        ko,
        quote,
        estimationDataResolver,
        getSectionsDetailsAction,
        shippingService,
        paymentMethodsService,
        sameAsShippingFlag
    ) {
        'use strict';

        quote.billingAddress.subscribe(function () {
            var sections = [],
                isNeedToUpdatePaymentMethods = estimationDataResolver.resolveBillingAddress()
                    && !sameAsShippingFlag.sameAsShipping();

            if (isNeedToUpdatePaymentMethods && !paymentMethodsService.isLoading()) {
                paymentMethodsService.isLoading(true);
                sections.push('paymentMethods');
            }

            if (sections.length) {
                getSectionsDetailsAction(sections).always(function () {
                    if (isNeedToUpdatePaymentMethods) {
                        paymentMethodsService.isLoading(false);
                    }
                });
            }
        });

        return {};
    }
);
