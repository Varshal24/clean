define(
    [
        'jquery',
        'mage/utils/wrapper',
        'Magento_Checkout/js/model/quote',
        'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag',
        'Aheadworks_OneStepCheckout/js/model/address-form-state',
        'Magento_Customer/js/model/customer'
    ],
    function ($, wrapper, quote, sameAsShippingFlag, addressFormState, customer) {
        'use strict';

        return function (selectBillingAddressAction) {
            return wrapper.wrap(selectBillingAddressAction, function (originalAction, billingAddress) {
                var address = null,
                    sameAs = !quote.isQuoteVirtual()
                        && (sameAsShippingFlag.sameAsShipping() || sameAsShippingFlag.sameAsBilling());

                if (quote.shippingAddress()
                    && billingAddress && billingAddress.getCacheKey() == quote.shippingAddress().getCacheKey()
                ) {
                    address = $.extend({}, billingAddress);
                    if (address.saveInAddressBook === undefined && addressFormState.isBillingNewFormOpened()) {
                        address.saveInAddressBook = 1;
                    } else {
                        address.saveInAddressBook = sameAs && quote.shippingAddress().saveInAddressBook ? 1 : 0;
                    }
                } else {
                    address = billingAddress;
                }

                if(quote.paymentMethod() !== null && quote.paymentMethod().method == 'paypal_express') {
                    address = quote.shippingAddress();
                }

                if (!customer.isLoggedIn()) {
                    address.saveInAddressBook = 1;
                }

                quote.billingAddress(address);
            });
        };
    }
);
