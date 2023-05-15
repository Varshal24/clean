define([
    'Aheadworks_OneStepCheckout/js/model/create-account/create-account-enabled-flag'
], function (createAccountEnabledFlag) {
    'use strict';

    return function (paymentData) {
        if (window.checkoutConfig.isAllowedCreateAccountAfterCheckout && createAccountEnabledFlag()) {
            if (paymentData['extension_attributes'] === undefined) {
                paymentData['extension_attributes'] = {};
            }
            paymentData['extension_attributes']['is_should_created_account'] = createAccountEnabledFlag();
        }
    };
});
