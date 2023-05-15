define(
    [
        'jquery',
        'underscore',
        'ko',
        'Magento_Ui/js/form/form',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'uiRegistry',
        'Aheadworks_OneStepCheckout/js/model/payment-methods-service',
        'Aheadworks_OneStepCheckout/js/model/checkout-data-completeness-logger',
        'Aheadworks_OneStepCheckout/js/model/address-form-state',
        'Aheadworks_OneStepCheckout/js/view/address-form-visibility-service',
        'Aheadworks_OneStepCheckout/js/view/street-resolver'
    ],
    function (
        $,
        _,
        ko,
        Component,
        addressList,
        addressConverter,
        registry,
        paymentMethodsService,
        completenessLogger,
        addressFormState,
        addressFormVisibilityService,
        streetResolver
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                saveInAddressBook: true,
                isStepVisible: true,
                scopeId: '',
                formFieldsetRegion: '',
                canValidate: false,
                paymentDeps: []
            },
            isFormInline: addressList().length == 0,

            /**
             * @inheritdoc
             */
            initialize: function () {
                var self = this;

                this._super();

                this._resolveAddress();
                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var addressData = self._getCheckoutAddressFormData();

                    if (addressData) {
                        var resultAddress = $.extend(
                            {},
                            checkoutProvider.get(self.scopeId),
                            _.omit(addressData, function (value) {
                                if (_.isString(value) && value == '') {
                                    return true;
                                }
                                return false;
                            })
                        );

                        resultAddress.street = $.extend({}, checkoutProvider.get(self.scopeId).street, addressData.street);
                        checkoutProvider.set(self.scopeId, resultAddress);
                    }
                    self._afterSetInitialAddressFormData();
                    checkoutProvider.on(self.scopeId, function (addressData) {
                        if (!addressFormState.isShippingNewFormOpened() && !addressData.customer_address_id) {
                            self._setCheckoutAddressFormData(addressData);
                        }
                    });
                    completenessLogger.bindAddressFieldsData(self.scopeId, checkoutProvider);
                });
                this.saveInAddressBook.subscribe(function (flag) {
                    var addressData = this._getCheckoutAddressFormData();

                    if (addressData) {
                        addressData.save_in_address_book = (flag ? 1 : 0);
                        this._setCheckoutAddressFormData(addressData);
                    }
                }, this);
                _.each(this.paymentDeps, function (depPath) {
                    paymentMethodsService.bindAddressFields(depPath);
                });
            },

            /**
             * Retrieve address form data from checkout data storage
             *
             * @returns {Object}
             */
            _getCheckoutAddressFormData: function () {
                return {};
            },

            /**
             * Set value shipping address form visible
             */
            _setValueShippingAddressFormVisible: function (value) {
                addressFormVisibilityService.isShippingAddressFormVisible(value);
            },

            /**
             * Set value billing address form visible
             */
            _setValueBillingAddressFormVisible: function (value) {
                addressFormVisibilityService.isBillingAddressFormVisible(value);
            },

            /**
             * Set address form data to checkout data storage
             *
             * @param {Object} addressData
             */
            _setCheckoutAddressFormData: function (addressData) {
            },

            /**
             * Resolve address
             */
            _resolveAddress: function () {
            },

            /**
             * Called after set initial address form data from checkout data storage
             */
            _afterSetInitialAddressFormData: function () {
            },

            /**
             * @inheritdoc
             */
            initObservable: function () {
                var addressData = this._getCheckoutAddressFormData();

                this._super();

                if (addressData && typeof addressData.save_in_address_book !== undefined) {
                    this.saveInAddressBook = addressData.save_in_address_book ? true : false;
                }
                this.observe(['saveInAddressBook', 'isStepVisible']);

                return this;
            },

            /**
             * @inheritdoc
             */
            validate: function () {
                this.source.set('params.invalid', false);
                if (this.useFormData()) {
                    this.source.trigger(this.scopeId + '.data.validate');

                    if (this.source.get(this.scopeId + '.custom_attributes')) {
                        this.source.trigger(this.scopeId + '.custom_attributes.data.validate');
                    }
                }
            },

            /**
             * Check if use form data
             *
             * @returns {boolean}
             */
            useFormData: function () {
                return this.isStepVisible() && this.isFormInline;
            },

            /**
             * Copy form data to quote address object
             *
             * @param {Object} quoteAddress
             * @returns {Object}
             */
            copyFormDataToQuoteData: function (quoteAddress) {
                var addressData = addressConverter.formAddressDataToQuoteAddress(
                    this.source.get(this.scopeId)
                );

                for (var field in addressData) {
                    if (addressData.hasOwnProperty(field) &&
                        quoteAddress.hasOwnProperty(field) &&
                        typeof addressData[field] != 'function' &&
                        _.isEqual(quoteAddress[field], addressData[field])
                    ) {
                        quoteAddress[field] = addressData[field];
                    } else if (typeof addressData[field] != 'function' &&
                        !_.isEqual(quoteAddress[field], addressData[field])) {
                        quoteAddress = addressData;
                        break;
                    }
                }

                if (quoteAddress.saveInAddressBook === undefined) {
                    quoteAddress.saveInAddressBook = this.saveInAddressBook() ? 1 : 0;
                }

                return quoteAddress;
            },

            /**
             * Fill up address form with address data
             *
             * @param {Object} address
             */
            fillUpFormWithAddress: function (address) {
                var addressData = addressConverter.quoteAddressToFormAddressData(address);
                addressData = _.omit(addressData, function (value) {
                    return value == null;
                });

                completenessLogger.isLoggingAllowed(false);
                this.source.set(this.scopeId, addressData);
                completenessLogger.isLoggingAllowed(true);
            },

            /**
             * Reset all address form fields
             */
            resetFormValidation: function () {
                var addressFieldSet = this.getRegion(this.formFieldsetRegion)();

                completenessLogger.isLoggingAllowed(false);
                if (addressFieldSet) {
                    addressFieldSet.forEach(function (childElem) {
                        this.resetFormElement(childElem);
                    }, this);
                }
                completenessLogger.isLoggingAllowed(true);
            },

            /**
             * Reset form element
             *
             * @param {Object} element
             */
            resetFormElement: function (element) {
                if (element) {
                    if (element.initChildCount) {
                        element.elems().forEach(function (elem) {
                            this.resetFormElement(elem);
                        }, this);
                    } else {
                        if (ko.isObservable(element.value)
                            && !element.value()
                            && typeof element.reset === 'function'
                        ) {
                            element.reset();
                        }
                    }
                }
            },

            /**
             * Merge address2 to address1
             *
             * @param {Object} address1
             * @param {Object} address2
             * @return {Object}
             */
            mergeAdress: function (address1, address2) {
                $.each(address2, function (key, value) {
                    if (typeof value !== 'function'
                        && typeof value !== 'undefined'
                        && key !== 'customerAddressId'
                    ) {
                        address1[key] = value;
                    }
                }.bind(this));

                return address1;
            },

            /**
             * Check if address form valid
             *
             * @return {Boolean}
             */
            isFormValid: function () {
                this.source.set('params.invalid', false);
                this.source.trigger(this.scopeId + '.data.validate');

                return !this.source.get('params.invalid');
            },

            /**
             * Check if address is new
             *
             * @param {Object} address
             * @return {Boolean}
             */
            isAddressNew: function (address) {
                if (!address) {
                    return false;
                }

                return address.getKey() === 'new-customer-address' ||  address.getKey() === 'new-customer-billing-address';
            },
            /**
             * Get street
             *
             * @param {string|object|array} street
             * @return string
             */
            getStreetString: function (street) {
                return streetResolver(street)
            }
        });
    }
);
