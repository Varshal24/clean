define(
    [
        'jquery',
        'braintree',
        'braintreeCheckoutPayPalAdapter',
        'braintreePayPalCheckout',
        'Magento_Checkout/js/model/quote',
        'Magento_CheckoutAgreements/js/view/checkout-agreements',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-checkout-data',
        'mage/translate'
    ],
    function (
        $,
        braintree,
        Braintree,
        paypalCheckout,
        quote,
        checkoutAgreements,
        additionalValidators,
        aggregateCheckoutData,
        $t
    ) {
        'use strict';

        return function (renderer) {
            return renderer.extend({
                defaults: {
                    mixinGrandTotalAmount: null,
                    addedEventsForAgreements: false
                },

                /**
                 * Set list of observable attributes
                 * @returns {exports.initObservable}
                 */
                initObservable: function () {
                    var self = this;

                    this._super();

                    //Dispose previous subscription from parent initObservable
                    //fix for old Magento_Braintree by Gene, new module name is PayPal_Braintree
                    if (self.component.indexOf('Magento_Braintree') !== -1) {
                        quote.paymentMethod._subscriptions.change.pop().dispose();
                        quote.paymentMethod.subscribe(function (value) {
                            var methodCode = value ? value.method : null;

                            if ((methodCode === 'braintree_paypal' || methodCode === 'braintree_paypal_vault') && self.methodSelected === false) {
                                self.reInitPayPal();
                            }
                            self.methodSelected = false;
                        });
                    }

                    quote.shippingAddress.subscribe(function () {
                        if (self.isActive() && !self.paymentMethodNonce) {
                            self.reInitPayPal();
                        }
                    });

                    this.mixinGrandTotalAmount = quote.totals()['base_grand_total'];

                    quote.totals.subscribe(function () {
                        if (self.mixinGrandTotalAmount !== quote.totals()['base_grand_total']) {
                            self.mixinGrandTotalAmount = quote.totals()['base_grand_total'];
                            var methodCode = quote.paymentMethod();

                            if (methodCode !== null && typeof methodCode === 'object') {
                                methodCode = methodCode.method;
                            }

                            if (methodCode === 'braintree_paypal' || methodCode === 'braintree_paypal_vault') {
                                self.reInitPayPal();
                            }
                        }
                    });

                    return this;
                },

                /**
                 * Get shipping address
                 * @returns {Object}
                 */
                getShippingAddress: function () {
                    var address = quote.shippingAddress();

                    if (_.isNull(address.postcode) || _.isUndefined(address.postcode)) {
                        return {};
                    }

                    return this._super();
                },

                /**
                 * Prepare data to place order
                 * @param {Object} data
                 */
                beforePlaceOrder: function (data) {
                    var self = this;
                    this.setPaymentMethodNonce(data.nonce);
                    this.customerEmail(data.details.email);
                    if (quote.isVirtual()) {
                        this.isReviewRequired(true);
                    } else {
                        aggregateCheckoutData.setCheckoutData().done(function () {
                            if (self.isRequiredBillingAddress() === '1' || quote.billingAddress() === null) {
                                if (typeof data.details.billingAddress !== 'undefined') {
                                    self.setBillingAddress(data.details, data.details.billingAddress);
                                } else {
                                    self.setBillingAddress(data.details, data.details.shippingAddress);
                                }
                            }

                            self.placeOrder();
                        });
                    }
                },

                /**
                 * Custom on init in order to correctly find the agreement checkboxes
                 */
                onInit: function (data, actions) {
                    if (!this.addedEventsForAgreements) {
                        var agreements = checkoutAgreements().agreements,
                            shouldDisableActions = false;

                        actions.disable();

                        _.each(agreements, function (item, index) {
                            if (checkoutAgreements().isAgreementRequired(item)) {
                                var paymentMethodCode = quote.paymentMethod().method,
                                    inputPaymentId = '#agreement_' + paymentMethodCode + '_' + item.agreementId,
                                    inputId = '#agreement__' + item.agreementId,
                                    inputEl = document.querySelector(inputPaymentId) || document.querySelector(inputId);

                                if (inputEl) {
                                    if (!inputEl.checked) {
                                        shouldDisableActions = true;
                                    }

                                    inputEl.addEventListener('change', function (event) {
                                        if (additionalValidators.validate()) {
                                            actions.enable();
                                        } else {
                                            actions.disable();
                                        }
                                    });
                                }
                            }
                        });

                        if (!shouldDisableActions) {
                            actions.enable();
                        }

                        this.addedEventsForAgreements = true;
                    }
                },

                /**
                 * Load Pay Pal Button
                 */
                loadPayPalButton: function (paypalCheckoutInstance, funding) {
                    var paypalPayment = Braintree.config.paypal,
                        onPaymentMethodReceived = Braintree.config.onPaymentMethodReceived;

                    var style = this._getStyle(funding);

                    if (Braintree.getBranding()) {
                        style.branding = Braintree.getBranding();
                    }
                    if (Braintree.getFundingIcons()) {
                        style.fundingicons = Braintree.getFundingIcons();
                    }

                    if (funding === 'credit') {
                        style.layout = "horizontal";
                        style.color = "darkblue";
                        Braintree.config.buttonId = this.clientConfig.buttonCreditId;
                    } else if (funding === 'paylater') {
                        style.layout = "horizontal";
                        style.color = "white";
                        Braintree.config.buttonId = this.clientConfig.buttonPaylaterId;
                    } else {
                        Braintree.config.buttonId = this.clientConfig.buttonPayPalId;
                    }
                    // Render
                    Braintree.config.paypalInstance = paypalCheckoutInstance;
                    var events = Braintree.events,
                        element = $('#' + Braintree.config.buttonId);

                    if (element.length) {
                        element.html('');
                    }

                    var button = paypal.Buttons({
                        fundingSource: funding,
                        env: Braintree.getEnvironment(),
                        style: style,
                        commit: true,
                        locale: Braintree.config.paypal.locale,

                        onInit: function (data, actions) {
                            this.onInit(data, actions);
                        }.bind(this),

                        createOrder: function () {
                            return paypalCheckoutInstance.createPayment(paypalPayment).catch(function (err) {
                                throw err.details.originalError.details.originalError.paymentResource;
                            });
                        },

                        onCancel: function (data) {
                            console.log('checkout.js payment cancelled', JSON.stringify(data, 0, 2));

                            if (typeof events.onCancel === 'function') {
                                events.onCancel();
                            }
                        },

                        onError: function (err) {
                            if (err.errorName === 'VALIDATION_ERROR' && err.errorMessage.indexOf('Value is invalid') !== -1) {
                                Braintree.showError($t('Address failed validation. Please check and confirm your City, State, and Postal Code'));
                            } else {
                                Braintree.showError($t("PayPal Checkout could not be initialized. Please contact the store owner."));
                            }
                            Braintree.config.paypalInstance = null;
                            console.error('Paypal checkout.js error', err);

                            if (typeof events.onError === 'function') {
                                events.onError(err);
                            }
                        }.bind(this),

                        onClick: function (data) {
                            if (!quote.isVirtual()) {
                                this.clientConfig.paypal.enableShippingAddress = true;
                                this.clientConfig.paypal.shippingAddressEditable = false;
                                this.clientConfig.paypal.shippingAddressOverride = this.getShippingAddress();
                            }

                            // To check term & conditions input checked - validate additional validators.
                            if (!additionalValidators.validate()) {
                                return false;
                            }

                            if (typeof events.onClick === 'function') {
                                events.onClick(data);
                            }
                        }.bind(this),

                        onApprove: function (data, actions) {
                            return paypalCheckoutInstance.tokenizePayment(data)
                                .then(function (payload) {
                                    onPaymentMethodReceived(payload);
                                });
                        }

                    });
                    if (button.isEligible() && $('#' + Braintree.config.buttonId).length) {
                        button.render('#' + Braintree.config.buttonId).then(function () {
                            Braintree.enableButton();
                            if (typeof Braintree.config.onPaymentMethodError === 'function') {
                                Braintree.config.onPaymentMethodError();
                            }
                        }.bind(this)).then(function (data) {
                            if (typeof events.onRender === 'function') {
                                events.onRender(data);
                            }
                        });
                    }
                },

                /**
                 * Get style settings and handling the compatibility error with version 4.4.0
                 *
                 * @param {string} funding
                 * @returns {Object}
                 */
                _getStyle: function (funding) {
                    var style;

                    try {
                        style = {
                            color: Braintree.getColor(),
                            shape: Braintree.getShape(),
                            layout: Braintree.getLayout(),
                            size: Braintree.getSize()
                        };
                    } catch (e) {
                        style = {
                            color: Braintree.getColor(funding),
                            shape: Braintree.getShape(funding),
                            layout: Braintree.getLayout(funding),
                            size: Braintree.getSize(funding),
                            tagline: Braintree.getTagline(funding),
                            label: Braintree.getLabel(funding)
                        };
                    }

                    return style;
                }
            });
        }
    }
);
