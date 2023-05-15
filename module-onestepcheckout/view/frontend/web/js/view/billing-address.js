define(
    [
        'ko',
        'Aheadworks_OneStepCheckout/js/view/address-abstract',
        'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Aheadworks_OneStepCheckout/js/model/checkout-data',
        'Magento_Checkout/js/action/create-billing-address',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/action/set-billing-address',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/customer-data',
        'Aheadworks_OneStepCheckout/js/view/billing-address/list',
        'Magento_Ui/js/model/messageList',
        'Aheadworks_OneStepCheckout/js/model/checkout-data-completeness-logger',
        'Magento_Checkout/js/model/address-converter',
        'Aheadworks_OneStepCheckout/js/action/update-customer-address',
        'Aheadworks_OneStepCheckout/js/model/address-form-state',
        'Aheadworks_OneStepCheckout/js/model/billing-address/current-address',
        'Aheadworks_OneStepCheckout/js/model/billing-address/step-state',
        'Aheadworks_OneStepCheckout/js/model/inventory/instore-pickup/state',
        'Aheadworks_OneStepCheckout/js/model/estimation-data-resolver',
        'uiRegistry',
        'Aheadworks_OneStepCheckout/js/model/billing-rate-service'
    ],
    function (
        ko,
        Component,
        sameAsShippingFlag,
        quote,
        checkoutDataResolver,
        checkoutData,
        createBillingAddressAction,
        selectBillingAddressAction,
        setBillingAddressAction,
        customer,
        customerData,
        addressList,
        globalMessageList,
        completenessLogger,
        addressConverter,
        updateCustomerAddress,
        addressFormState,
        currentBillingAddress,
        billingAddressStepState,
        instorePickupState,
        estimationDataResolver,
        uiRegistry
    ) {
        'use strict';

        var countryData = customerData.get('directory-data');

        return Component.extend({
            defaults: {
                scopeId: 'billingAddress',
                formFieldsetRegion: 'billing-address-fieldset',
                template: 'Aheadworks_OneStepCheckout/billing-address',
                availableForMethods: [],
                isQuoteVirtual: quote.isQuoteVirtual(),
                isAddressListVisible: addressList().length > 0,
                isAddressSpecified: false,
                customerHasAddresses: false,
                isAddNewAddressLinkVisible: true,
                errorValidationMessage: '',
                isCurrentAddressNew: currentBillingAddress.isNew,
            },
            canUseShippingAddress: billingAddressStepState.canUseShippingAddress,

            newAddress: {},

            /**
             * @inheritdoc
             */
            initialize: function () {
                this._super();

                this.canUseShippingAddress.subscribe(
                    function (canUseShippingAddress) {
                        this.isAddressSameAsShipping(canUseShippingAddress);
                        this.onUseShippingAddress();
                    },
                    this
                );

                if (quote.paymentMethod()) {
                    this.isStepVisible(this._isAvailableForMethod(quote.paymentMethod().method));
                }
                quote.paymentMethod.subscribe(function (method) {
                    var isShown = method ? this._isAvailableForMethod(method.method) : true;
                    this.isStepVisible(isShown);
                    if (!this.isStepVisible()) {
                        this.isAddressSameAsShipping(true);
                    }
                }, this);

                quote.billingAddress.subscribe(function (billingAddress) {
                    if (this.isFormInline) {
                        if (!currentBillingAddress.address() || currentBillingAddress.address().getKey() !== billingAddress.getKey()) {
                            currentBillingAddress.address(billingAddress);
                        }
                    }
                    if (!this.isAddressSpecified() && billingAddress) {
                        checkoutData.setSelectedBillingAddress(billingAddress.getKey());
                    }
                }, this);

                this.isAddressSpecified.subscribe(function (flag) {
                    if (flag) {
                        this.errorValidationMessage('');
                    }
                }, this);

                this.prepareNewBillingAddress();
                this.isAddNewAddressLinkVisible(!this.isNewAddressAlreadyDefined());
                completenessLogger.bindSelectedAddressData('billingAddress', quote.billingAddress);
                currentBillingAddress.address.subscribe(this.onCurrentAddressChange, this);

                if (checkoutData.getAddressTypeToDisplayFirst() == 'billing') {
                    this.saveInAddressBook(1);
                }

                return this;
            },

            /**
             * @inheritdoc
             */
            initObservable: function () {
                this._super();
                this.observe([
                    'isAddressDetailsVisible',
                    'isAddressListVisible',
                    'isAddressSpecified',
                    'isVisibleSameAsShippingFlag',
                    'errorValidationMessage',
                    'isAddNewAddressLinkVisible',
                    'isCurrentAddressNew',
                    'isSaveNewAddressCheckboxVisible',
                    'isAddressSameAsShipping',
                    'billingAddressForDetails'
                ]);

                this.isCurrentAddressNew(addressList().length === 0);

                //Resolved current billing address used to display as text details
                this.billingAddressForDetails = ko.computed(function () {
                    if (checkoutData.getAddressTypeToDisplayFirst() == 'billing'
                        || !sameAsShippingFlag.sameAsShipping()
                        || this.isQuoteVirtual) {
                        return null;
                    }

                    var addressForDetails = this.isFormInline
                        ? createBillingAddressAction(
                            checkoutData.getShippingAddressFromData()
                            || window.checkoutConfig.shippingAddressFromData
                        )
                        : quote.shippingAddress();

                    currentBillingAddress.address(addressForDetails);

                    return addressForDetails;
                }, this);

                this.isAddressSameAsShipping = ko.pureComputed({
                    read: function () {
                        if (checkoutData.getAddressTypeToDisplayFirst() == 'shipping' && !instorePickupState.isStorePickupSelected()) {
                            return sameAsShippingFlag.sameAsShipping();
                        }

                        return false;
                    },
                    write: function (value) {
                        if (checkoutData.getAddressTypeToDisplayFirst() == 'shipping') {
                            sameAsShippingFlag.sameAsShipping(value);
                        } else {
                            sameAsShippingFlag.sameAsShipping(false);
                        }
                    },

                    owner: this
                });

                this.isAddressSpecified = ko.computed(function () {
                    if (!this.isQuoteVirtual && this.isAddressSameAsShipping()) {
                        return quote.billingAddress() != null;
                    } else {
                        return checkoutData.getSelectedBillingAddress() != null
                            || checkoutData.getNewCustomerBillingAddress() != null;
                    }
                }, this);

                this.isVisibleSameAsShippingFlag = ko.computed(function () {
                    return this.canUseShippingAddress()
                        && checkoutData.getAddressTypeToDisplayFirst() == 'shipping';
                }, this),

                //Flag to display billing address as text
                this.isAddressDetailsVisible = ko.computed(function () {
                    if (!this.isAddressSameAsShipping()) {
                        return false;
                    }

                    return !addressFormState.isBillingNewFormOpened()
                        && ((!customer.isLoggedIn() && (this.isAddressSameAsShipping() || this.isQuoteVirtual))
                            || customer.isLoggedIn());
                }, this);

                //Flag to display grid with billing address list
                this.isAddressListVisible = ko.computed(function () {
                    var sameAsShipping = this.canUseShippingAddress() ? this.isAddressSameAsShipping(): false;

                    return !addressFormState.isBillingNewFormOpened() && !sameAsShipping;
                }, this);

                this.isAddressFormVisible = ko.computed(function () {
                    if (this.isFormInline) {
                        this._setValueBillingAddressFormVisible(false);
                        return quote.isQuoteVirtual()
                            || !quote.isQuoteVirtual() && !this.isAddressSameAsShipping()
                    } else {
                        var result = addressFormState.isBillingNewFormOpened()
                            && (!currentBillingAddress.isValid() || this.isAddressNew(currentBillingAddress.address()));
                        if (result) {
                            this._setValueBillingAddressFormVisible(true);
                            return true;
                        } else {
                            this._setValueBillingAddressFormVisible(false);
                            return false;
                        }
                    }
                }, this);
                this.isExistingAddressOpenToEdit = ko.computed(function () {
                    var address = currentBillingAddress.address();
                    return address && !this.isAddressNew(address);
                }, this);

                this.isSaveNewAddressCheckboxVisible = ko.computed(function () {
                    return customer.isLoggedIn() && this.isCurrentAddressNew();
                }, this);

                // check is billing address form is opened to input data
                addressFormState.isBillingFormOpened(!sameAsShippingFlag.sameAsShipping() && (this.isFormInline || this.isAddressFormVisible()));
                this.isAddressFormVisible.subscribe(function (isVisible) {
                    addressFormState.isBillingFormOpened(isVisible)
                }, this);

                sameAsShippingFlag.sameAsShipping.subscribe(function (isBillingSameAsShipping) {
                    addressFormState.isBillingFormOpened(!isBillingSameAsShipping && (this.isFormInline || this.isAddressFormVisible()))
                }, this);

                return this;
            },

            /**
             * Check if new address already defined in address book
             *
             * @return {Boolean}
             */
            isNewAddressAlreadyDefined: function () {
                return addressList.some(function (address) {
                    return this.isAddressNew(address);
                }, this);
            },

            /**
             * On current address change
             *
             * @param {Object} billingAddress
             */
            onCurrentAddressChange: function (billingAddress) {
                var isValid;

                this.fillUpFormWithAddress(billingAddress);
                this.resetFormValidation();

                isValid = this.isFormValid();
                if (this.isAddressNew(billingAddress)) {
                    currentBillingAddress.isNew(true);
                    this.resetFormValidation();
                } else {
                    currentBillingAddress.isNew(false);
                    addressFormState.isBillingNewFormOpened(!isValid);
                }

                currentBillingAddress.isValid(isValid);
                if (isValid && quote.billingAddress()) {
                    selectBillingAddressAction(currentBillingAddress.address());
                    checkoutData.setSelectedBillingAddress(currentBillingAddress.address().getKey());
                }
            },

            /**
             * @inheritDoc
             */
            focusInvalid: function () {
                addressFormState.isBillingNewFormOpened(true);

                return this._super();
            },

            /**
             * @inheritdoc
             */
            _getCheckoutAddressFormData: function () {
                return checkoutData.getBillingAddressFromData();
            },

            /**
             * @inheritdoc
             */
            _setCheckoutAddressFormData: function (addressData) {
                var billingAddressComponent = uiRegistry.get('index = billingAddress');
                this._super();
                if (billingAddressComponent && !addressData.hasOwnProperty('save_in_address_book')) {
                    addressData.save_in_address_book = billingAddressComponent.saveInAddressBook() ? 1 : 0
                }
                checkoutData.setBillingAddressFromData(addressData);
            },

            /**
             * @inheritdoc
             */
            _resolveAddress: function () {
                checkoutDataResolver.resolveBillingAddress();
            },

            /**
             * Get country name
             *
             * @param {string} countryId
             * @return string
             */
            getCountryName: function (countryId) {
                return countryData()[countryId] != undefined ? countryData()[countryId].name : '';
            },

            /**
             * On use shipping address checkbox click event handler
             *
             * @returns {boolean}
             */
            onUseShippingAddress: function () {
                var addressData,
                    newBillingAddress;

                if (checkoutData.getAddressTypeToDisplayFirst() == 'shipping') {
                    if (this.isAddressSameAsShipping()) {
                        if (this.isFormInline) {
                            addressData = this.source.get('billingAddress');
                            this.newAddress = createBillingAddressAction(addressData);
                        }
                        selectBillingAddressAction(quote.shippingAddress());
                        this.updateAddresses();
                        currentBillingAddress.address(quote.shippingAddress());
                        checkoutData.setSelectedBillingAddress(quote.shippingAddress().getKey());
                        addressFormState.isBillingNewFormOpened(false);
                    } else {
                        if (this.isFormInline) {
                            addressData = addressConverter.quoteAddressToFormAddressData(this.newAddress);
                            newBillingAddress = this.newAddress;
                            selectBillingAddressAction(newBillingAddress);
                            currentBillingAddress.address(newBillingAddress);
                            currentBillingAddress.isNew(false);
                            checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                            checkoutData.setNewCustomerBillingAddress(addressData);
                        } else {
                            if (quote.billingAddress()) {
                                currentBillingAddress.address(quote.billingAddress());
                                checkoutData.setSelectedBillingAddress(quote.billingAddress().getKey());
                            } else {
                                checkoutData.setSelectedBillingAddress(null);
                            }
                        }
                    }
                }

                return true;
            },

            /**
             * Check if use form data
             *
             * @returns {boolean}
             */
            useFormData: function () {
                var sameAsShipping = this.canUseShippingAddress() ? this.isAddressSameAsShipping(): false;

                return this.isStepVisible()
                    && (addressFormState.isBillingNewFormOpened() || this.isFormInline)
                    && !sameAsShipping;
            },

            /**
             * Restore previous billing address if it is specified
             */
            restoreBillingAddress: function () {
                var previousAddress = currentBillingAddress.getPreviousAddress();

                if (previousAddress) {
                    currentBillingAddress.address(previousAddress);
                    checkoutData.setSelectedBillingAddress(previousAddress.getKey());
                }
            },

            /**
             * Update shipping and billing addresses
             */
            updateAddresses: function () {
                if (window.checkoutConfig.reloadOnBillingAddress ||
                    !window.checkoutConfig.displayBillingOnPaymentMethod
                ) {
                    setBillingAddressAction(globalMessageList);
                }
            },

            /**
             * Check if billing address is available for payment method
             *
             * @param {string} methodCode
             * @returns {boolean}
             */
            _isAvailableForMethod: function (methodCode) {
                methodCode = methodCode.replace(/vault_(\d+)/g, 'vault');

                return this.availableForMethods.indexOf(methodCode) !== -1;
            },

            /**
             * On update address click event handler
             */
            onUpdateAddressClick: function () {
                var currentAddress, newAddress, addressFormData;

                if (this.isFormValid()) {
                    currentBillingAddress.isValid(true);
                    addressFormData = this.source.get(this.scopeId);
                    newAddress = addressConverter.formAddressDataToQuoteAddress(addressFormData);
                    currentAddress = currentBillingAddress.address();
                    if (currentAddress) {
                        this.mergeAdress(currentAddress, newAddress);
                        currentBillingAddress.address(currentAddress);
                        updateCustomerAddress(currentAddress);
                        selectBillingAddressAction(currentBillingAddress.address());
                        checkoutData.setSelectedBillingAddress(currentBillingAddress.address().getKey());
                        if (this.isAddressSameAsShipping()) {
                            quote.billingAddress(currentBillingAddress.address());
                        }
                    } else {
                        currentBillingAddress.address(newAddress);
                        selectBillingAddressAction(newAddress);
                        checkoutData.setSelectedBillingAddress(newAddress);
                    }
                    addressFormState.isBillingNewFormOpened(false);
                }
            },

            /**
             * On save new address button click event handler
             */
            onSaveNewAddressClick: function () {
                var addressData,
                    newBillingAddress,
                    isValid = this.isFormValid();

                currentBillingAddress.isValid(isValid);
                if (isValid) {
                    addressData = this.source.get(this.scopeId);
                    if (customer.isLoggedIn() && !this.customerHasAddresses) {
                        this.saveInAddressBook(1);
                    }

                    addressData['save_in_address_book'] = this.saveInAddressBook() ? 1 : 0;
                    addressData['telephone'] = addressData['telephone'].trim();
                    newBillingAddress = createBillingAddressAction(addressData);
                    this.addNewBillingAddressToList(newBillingAddress);

                    selectBillingAddressAction(newBillingAddress);
                    checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                    checkoutData.setNewCustomerBillingAddress(addressData);

                    currentBillingAddress.address(newBillingAddress);
                    addressFormState.isBillingNewFormOpened(false);
                    this.updateAddresses();
                    this.isAddNewAddressLinkVisible(false);
                }
            },

            /**
             * Add new billing address to list
             *
             * Shipping address is added by default, but billing address must be added manually
             *
             * @param {Object} newBillingAddress
             */
            addNewBillingAddressToList: function (newBillingAddress) {
                var isAddressUpdated = addressList().some(function (currentAddress, index, addresses) {
                    if (currentAddress.getKey() === newBillingAddress.getKey()) {
                        addresses[index] = newBillingAddress;
                        return true;
                    }
                    return false;
                });

                if (!isAddressUpdated) {
                    addressList.push(newBillingAddress);
                } else {
                    addressList.valueHasMutated();
                }
            },

            /**
             * On cancel button click event handler
             */
            onCancelClick: function () {
                this._restoreSameAsShippingFlag();
                if (this.isAddressSameAsShipping()) {
                    checkoutData.setSelectedBillingAddress(quote.billingAddress().getKey());
                    currentBillingAddress.address(quote.billingAddress());
                } else {
                    this.restoreBillingAddress();
                }
                addressFormState.isBillingNewFormOpened(false);
            },

            /**
             * Restore same as shipping flag
             */
            _restoreSameAsShippingFlag: function () {
                if (quote.shippingAddress()
                    && quote.billingAddress()
                    && quote.shippingAddress().getKey() == quote.billingAddress().getKey()
                    && !this.isQuoteVirtual
                ) {
                    this.isAddressSameAsShipping(true);
                }
            },

            /**
             * On add new address link click event handler
             */
            onAddNewAddressClick: function () {
                var currentAddress = currentBillingAddress.address();
                currentBillingAddress.previousAddress = currentAddress || quote.billingAddress();
                if (!currentAddress || currentAddress.getKey() !== this.newAddress.getKey()) {
                    currentBillingAddress.address(this.newAddress);
                }

                addressFormState.isBillingNewFormOpened(true);
            },

            /**
             * @inheritdoc
             */
            validate: function () {
                var address;

                if (this.isStepVisible()) {
                    if (this.isFormInline || this.isAddressFormVisible()) {
                        this._super();
                    } else {
                        address = currentBillingAddress.address() || quote.billingAddress();
                        if (address) {
                            this.fillUpFormWithAddress(address);
                            this.resetFormValidation();
                            if (!this.isFormValid()) {
                                currentBillingAddress.address(address);
                            }
                        }
                    }
                    if (!this.isFormInline && !this.isAddressSpecified()) {
                        this.source.set('params.invalid', true);
                        this.errorValidationMessage('Please specify a billing address.');
                    }
                }
            },

            /**
             * Prepare new billing address
             */
            prepareNewBillingAddress: function () {
                var newBillingAddress,
                    newAddressFormData = checkoutData.getNewCustomerBillingAddress() || this.source.get('billingAddress');

                newBillingAddress = createBillingAddressAction(newAddressFormData);
                if (checkoutData.getNewCustomerBillingAddress()) {
                    this.addNewBillingAddressToList(newBillingAddress);
                }

                this.newAddress = newBillingAddress;
                this.customerHasAddresses = addressList().length > 0;
            }
        });
    }
);
