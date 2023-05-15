define([
    'jquery',
    'mage/utils/wrapper',
    'Aheadworks_OneStepCheckout/js/model/newsletter/assigner',
    'Aheadworks_OneStepCheckout/js/model/order-note/assigner',
    'Aheadworks_OneStepCheckout/js/model/delivery-date/assigner',
    'Aheadworks_OneStepCheckout/js/model/customer-information/assigner',
    'Aheadworks_OneStepCheckout/js/model/create-account/assigner',
    'Magento_Checkout/js/model/full-screen-loader',
    'Aheadworks_OneStepCheckout/js/model/update-biliing-address-from-checkout-data'
], function (
    $,
    wrapper,
    newsletterAssigner,
    orderNoteAssigner,
    deliveryDateAssigner,
    customerInformationAssigner,
    createAccountAssigner,
    fullScreenLoader,
    updateBiliingAddressFromCheckoutData
) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            newsletterAssigner(paymentData);
            orderNoteAssigner(paymentData);
            deliveryDateAssigner(paymentData);
            customerInformationAssigner(paymentData);
            createAccountAssigner(paymentData);
            updateBiliingAddressFromCheckoutData()
            return originalAction(paymentData, messageContainer).fail(
                function () {
                    fullScreenLoader.stopLoader();
                }
            );
        });
    };
});
