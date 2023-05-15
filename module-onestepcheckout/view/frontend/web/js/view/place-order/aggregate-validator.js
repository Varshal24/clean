define(
    [
        'jquery',
        'underscore',
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'Aheadworks_OneStepCheckout/js/model/payment-validation-invoker',
        'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-checkout-data'
    ],
    function (
        $,
        _,
        registry,
        quote,
        paymentValidationInvoker,
        aggregateCheckoutData
    ) {
        'use strict';

        var checkoutConfig = window.checkoutConfig,
            isEnabledDefaultPlaceOrderButton = checkoutConfig
                ? checkoutConfig.isEnabledDefaultPlaceOrderButton
                : false;

        return {

            /**
             * Perform overall checkout data validation
             *
             * @returns {Deferred}
             */
            validate: function () {
                var deferred = $.Deferred(),
                    isValid = true;

                isValid = this.groupValidateMethods(isValid);

                this._validatePaymentMethod(isValid).done(function () {
                    if (isValid) {
                        deferred.resolve();
                    }
                });

                return deferred;
            },

            /**
             * Group Validate Methods
             *
             * @param {boolean} isValid
             * @returns {Boolean}
             */
            groupValidateMethods: function (isValid) {
                if (!this._validateCustomerInformationEmail(isValid)) {
                    isValid = false;
                }
                if (!this._validateCustomerInformation(isValid)) {
                    isValid = false;
                }
                if (!this._validateAddresses(isValid)) {
                    isValid = false;
                }
                if (!this._validateShippingMethod(isValid)) {
                    isValid = false;
                }
                if (!this._validateDeliveryDateFormData(isValid)) {
                    isValid = false;
                }

                // checkout data must be set before placing order,
                // revise in case better place is found to put this logic
                if (isValid && isEnabledDefaultPlaceOrderButton) {
                    aggregateCheckoutData.setCheckoutData().done(function () {
                        return true;
                    });
                } else {
                    return isValid;
                }
            },

            /**
             * Validate customer information email
             *
             * @param {boolean} isValid
             * @returns {Boolean}
             */
            _validateCustomerInformationEmail: function (isValid) {
                var customerInfoEmailComponent = registry.get('checkout.customer-information.email'),
                isValid = customerInfoEmailComponent.validateEmailOnPlaceOrder();
                if (!isValid) {
                    customerInfoEmailComponent.scrollInvalid()
                }

                return isValid;
            },

            /**
             * Validate customer information data
             *
             * @param {boolean} isValid
             * @returns {Boolean}
             */
            _validateCustomerInformation: function (isValid) {
                var customerInfoComponent = registry.get('checkout.customer-information.customer-info-fields'),
                    provider = registry.get('checkoutProvider');

                if (customerInfoComponent.isCustomerInfoSectionDisplayed) {
                    customerInfoComponent.validate();
                    if (isValid && provider.get('params.invalid')) {
                        isValid = false;
                        customerInfoComponent.focusInvalid();
                    }
                }

                return isValid;
            },

            /**
             * Validate addresses data
             *
             * @param {boolean} isValid
             * @returns {Boolean}
             */
            _validateAddresses: function (isValid) {
                var provider = registry.get('checkoutProvider');

                _.each(['checkout.shippingAddress', 'checkout.paymentMethod.billingAddress'], function (query) {
                    var addressComponent = registry.get(query);

                    addressComponent.validate();
                    if (isValid && provider.get('params.invalid')) {
                        isValid = false;
                        addressComponent.focusInvalid();
                    }
                }, this);

                return isValid;
            },

            /**
             * Validate shipping address data
             *
             * @param {boolean} isValid
             * @param {boolean} isFocused
             * @returns {Boolean}
             */
            validateShippingAddress: function (isValid, isFocused) {
                var provider = registry.get('checkoutProvider');

                _.each(['checkout.shippingAddress'], function (query) {
                    var addressComponent = registry.get(query);

                    addressComponent.validate();
                    if (isValid && provider.get('params.invalid')) {
                        isValid = false;
                        if (isFocused) {
                            addressComponent.focusInvalid();
                        }
                    }
                }, this);

                return isValid;
            },

            /**
             * Validate shipping method
             *
             * @param {boolean} isValid
             * @returns {boolean}
             */
            _validateShippingMethod: function (isValid) {
                var shippingMethodComponent = registry.get('checkout.shippingMethod'),
                    provider = registry.get('checkoutProvider');

                shippingMethodComponent.validate();
                if (isValid && provider.get('params.invalid')) {
                    isValid = false;
                    shippingMethodComponent.scrollInvalid();
                }

                return isValid;
            },

            /**
             * Validate delivery date form data
             *
             * @param {boolean} isValid
             * @returns {boolean}
             */
            _validateDeliveryDateFormData: function (isValid) {
                var deliveryDateComponent = registry.get('checkout.shippingMethod.delivery-date'),
                    provider = registry.get('checkoutProvider');

                deliveryDateComponent.validate();
                if (isValid && provider.get('params.invalid')) {
                    isValid = false;
                    deliveryDateComponent.focusInvalid();
                }

                return isValid;
            },

            /**
             * Validate payment method
             *
             * @param {boolean} isValid
             * @returns {Deferred}
             */
            _validatePaymentMethod: function (isValid) {
                var methodListComponent = registry.get('checkout.paymentMethod.methodList'),
                    methodCode,
                    methodRenderer;

                if (quote.paymentMethod()) {
                    methodCode = quote.paymentMethod().method;
                    methodRenderer = methodListComponent.getChild(methodCode);

                    return paymentValidationInvoker.invokeValidate(methodRenderer, methodCode);
                } else {
                    if (isValid && !methodListComponent.validate()) {
                        isValid = false;
                        methodListComponent.scrollInvalid();
                    }

                    return isValid
                        ? $.Deferred().resolve()
                        : $.Deferred().reject();
                }
            }
        };
    }
);
