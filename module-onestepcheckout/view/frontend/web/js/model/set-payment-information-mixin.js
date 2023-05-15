define([
    'jquery',
    'mage/utils/wrapper',
    'Aheadworks_OneStepCheckout/js/model/newsletter/assigner',
    'Aheadworks_OneStepCheckout/js/model/order-note/assigner',
    'Aheadworks_OneStepCheckout/js/model/delivery-date/assigner',
    'Aheadworks_OneStepCheckout/js/model/customer-information/assigner',
    'Aheadworks_OneStepCheckout/js/model/create-account/assigner'
], function (
    $,
    wrapper,
    newsletterAssigner,
    orderNoteAssigner,
    deliveryDateAssigner,
    customerInformationAssigner,
    createAccountAssigner
) {
    'use strict';

    return function (setPaymentInformationAction) {
        return wrapper.wrap(setPaymentInformationAction, function (originalAction, messageContainer, paymentData) {
            newsletterAssigner(paymentData);
            orderNoteAssigner(paymentData);
            deliveryDateAssigner(paymentData);
            customerInformationAssigner(paymentData);
            createAccountAssigner(paymentData);

            return originalAction(messageContainer, paymentData);
        });
    };
});
