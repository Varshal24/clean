define(
    [
        'jquery',
        'underscore',
        'mage/utils/wrapper',
        'Magento_Paypal/js/in-context/paypal-sdk',
        'Magento_Customer/js/customer-data',
        'uiRegistry'
    ],
    function ($, _, wrapper, paypalSdk, customerData, uiRegistry) {
        'use strict';

        /**
         * Triggers beforePayment action on PayPal buttons
         *
         * @param {Object} clientConfig
         * @returns {Object} jQuery promise
         */
        function performCreateOrder(clientConfig) {
            var params = {
                'quote_id': clientConfig.quoteId,
                'customer_id': clientConfig.customerId || '',
                'form_key': clientConfig.formKey,
                button: clientConfig.button
            };

            return $.Deferred(function (deferred) {
                clientConfig.rendererComponent.beforePayment(deferred.resolve, deferred.reject).then(function () {
                    $.post(clientConfig.getTokenUrl, params).done(function (res) {
                        clientConfig.rendererComponent.afterPayment(res, deferred.resolve, deferred.reject);
                    }).fail(function (jqXHR, textStatus, err) {
                        clientConfig.rendererComponent.catchPayment(err, deferred.resolve, deferred.reject);
                    });
                });
            }).promise();
        }

        /**
         * Check if checkout page
         *
         * @returns {boolean}
         */
        function isCheckoutPage() {
            return window.location.pathname === '/checkout/';
        }

        /**
         * Triggers beforeOnAuthorize action on PayPal buttons
         * @param {Object} clientConfig
         * @param {Object} data
         * @param {Object} actions
         * @returns {Object} jQuery promise
         */
        function performOnApprove(clientConfig, data, actions) {
            var params = {
                paymentToken: data.orderID,
                payerId: data.payerID,
                paypalFundingSource: customerData.get('paypal-funding-source'),
                'form_key': clientConfig.formKey
            };

            return $.Deferred(function (deferred) {
                clientConfig.rendererComponent.beforeOnAuthorize(deferred.resolve, deferred.reject, actions)
                    .then(function () {
                        $.post(clientConfig.onAuthorizeUrl, params).done(function (res) {
                            clientConfig.rendererComponent
                                .afterOnAuthorize(res, deferred.resolve, deferred.reject, actions);
                            customerData.set('paypal-funding-source', '');
                        }).fail(function (jqXHR, textStatus, err) {
                            clientConfig.rendererComponent.catchOnAuthorize(err, deferred.resolve, deferred.reject);
                            customerData.set('paypal-funding-source', '');
                        });
                    });
            }).promise();
        }

        return function (clientConfig) {
            if (!isCheckoutPage()) {
                return clientConfig;
            } else {
                var quote = require('Magento_Checkout/js/model/quote'),
                    aggregateValidator = require('Aheadworks_OneStepCheckout/js/view/place-order/aggregate-validator');

                return wrapper.wrap(clientConfig, function (originalAction, clientConfig, element) {
                    paypalSdk(clientConfig.sdkUrl, clientConfig.dataAttributes).done(function (paypal) {
                        paypal.Buttons({
                            style: clientConfig.styles,

                            /**
                             * onInit is called when the button first renders
                             * @param {Object} data
                             * @param {Object} actions
                             */
                            onInit: function (data, actions) {
                                $(document).on('select-paypal-express-payment', function() {
                                    if (aggregateValidator.validateShippingAddress(true)) {
                                        clientConfig.rendererComponent.validate(actions);
                                    }
                                });

                                quote.shippingAddress.subscribe(function () {
                                    if (aggregateValidator.validateShippingAddress(true)) {
                                        clientConfig.rendererComponent.validate(actions);
                                    }
                                }.bind(actions));

                                actions.disable();
                            },

                            /**
                             * Triggers beforePayment action on PayPal buttons
                             * @returns {Object} jQuery promise
                             */
                            createOrder: function () {
                                return performCreateOrder(clientConfig);
                            },

                            /**
                             * Triggers beforeOnAuthorize action on PayPal buttons
                             * @param {Object} data
                             * @param {Object} actions
                             */
                            onApprove: function (data, actions) {
                                performOnApprove(clientConfig, data, actions);
                            },

                            /**
                             * Execute logic on Paypal button click
                             */
                            onClick: function (data) {
                                customerData.set('paypal-funding-source', data.fundingSource);
                                if (isCheckoutPage()) {
                                    if (aggregateValidator.validateShippingAddress(true, true)) {
                                        clientConfig.rendererComponent.validate();
                                        clientConfig.rendererComponent.onClick();
                                    } else {
                                        var shippingAddress = uiRegistry.get('index = shippingAddress');
                                        shippingAddress.isAddressSameAsBilling(false);

                                        if (clientConfig.rendererComponent.actions) {
                                            clientConfig.rendererComponent.actions.disable()
                                        }
                                    }
                                } else {
                                    clientConfig.rendererComponent.validate();
                                    clientConfig.rendererComponent.onClick();
                                }
                            },

                            /**
                             * Process cancel action
                             * @param {Object} data
                             * @param {Object} actions
                             */
                            onCancel: function (data, actions) {
                                clientConfig.rendererComponent.onCancel(data, actions);
                            },

                            /**
                             * Process errors
                             *
                             * @param {Error} err
                             */
                            onError: function (err) {
                                clientConfig.rendererComponent.onError(err);
                            }
                        }).render(element);
                    });
                });
            }
        };
    }
);
