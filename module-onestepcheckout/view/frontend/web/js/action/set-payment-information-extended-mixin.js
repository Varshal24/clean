define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer'
], function (
    $,
    wrapper,
    quote,
    customer
) {
    'use strict';

    return function (selectPaymentInformationExtendedAction) {
        return wrapper.wrap(selectPaymentInformationExtendedAction, function (originalAction, messageContainer, paymentData, skipBilling) {
            if (!customer.isLoggedIn() && !quote.guestEmail) {
                return $.Deferred().reject();
            }

            return originalAction(messageContainer, paymentData, skipBilling);
        });
    };
});
