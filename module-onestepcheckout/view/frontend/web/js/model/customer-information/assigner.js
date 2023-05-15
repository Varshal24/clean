define([
    'Aheadworks_OneStepCheckout/js/model/customer-information/provider',
], function (provider) {
    'use strict';

    var isCustomerInfoSectionDisplayed = window.checkoutConfig.isCustomerInfoSectionDisplayed;

    return function (paymentData) {
        if (isCustomerInfoSectionDisplayed) {
            if (paymentData['extension_attributes'] === undefined) {
                paymentData['extension_attributes'] = {};
            }
            paymentData['extension_attributes']['aw_osc_customer_info'] = provider.getData();
        }
    };
});
