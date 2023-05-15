define(
    [
        'jquery',
        'underscore',
        'ko',
        'Aheadworks_OneStepCheckout/js/view/address-abstract',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Aheadworks_OneStepCheckout/js/model/checkout-data',
        'Magento_Customer/js/model/address-list',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Aheadworks_OneStepCheckout/js/model/address-form-state',
        'Aheadworks_OneStepCheckout/js/model/shipping-address/current-address',
        'Aheadworks_OneStepCheckout/js/model/billing-address/current-address',
        'Aheadworks_OneStepCheckout/js/model/shipping-address/step-state',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'uiRegistry',
        'Aheadworks_OneStepCheckout/js/model/inventory/instore-pickup/state',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/model/address-converter',
        'Aheadworks_OneStepCheckout/js/action/update-customer-address',
        'Magento_Checkout/js/action/select-billing-address',
        'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag',
        'Aheadworks_OneStepCheckout/js/model/shipping-rate-service',
        'Aheadworks_OneStepCheckout/js/model/estimation-data-resolver'
    ],
    function (
        $,
        _,
        ko,
        Component,
        checkoutDataResolver,
        checkoutData,
        addressList,
        customer,
        quote,
        createShippingAddressAction,
        selectShippingAddressAction,
        addressFormState,
        currentShippingAddress,
        currentBillingAddress,
        shippingAddressStepState,
        shippingRatesValidator,
        uiRegistry,
        instorePickupState,
        customerData,
        addressConverter,
        updateCustomerAddress,
        selectBillingAddressAction,
        sameAsShippingFlag
    ) {
        'use strict';

        var countryData = customerData.get('directory-data');

        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/shipping-address',
                scopeId: 'shippingAddress',
                newAddressForm: '[data-role=new-shipping-address-form]',
                formFieldsetRegion: 'shipping-address-fieldset',
                isAddNewAddressLinkVisible: true,
                isCurrentAddressNew: currentShippingAddress.isNew,
                isAddressListVisible: true,
                isAddressFormVisible: false,
                isStepVisible: shippingAddressStepState.isVisible(),
                listens: {
                    '${ $.provider }:billingAddress.country_id:value': 'afterCountryUpdate'
                }
            },
            newAddress: {},

            /**
             * @inheritdoc
             */
            initialize: function () {
                this._super();

                this.prepareNewShippingAddress();
                this.isAddNewAddressLinkVisible(!this.isNewAddressAlreadyDefined());

                addressFormState.isShippingNewFormOpened.subscribe(function (isShown) {
                    if (isShown) {
                        this._openNewShippingAddressForm();
                    } else {
                        this._closeNewShippingAddressForm();
                    }
                }, this);

                currentShippingAddress.address.subscribe(this.onCurrentAddressChange, this);

                currentBillingAddress.address.subscribe(this.onUseBillingAddress, this);

                shippingAddressStepState.isVisible.subscribe(
                    function (isShippingAddressStepVisible) {
                        this.isStepVisible(isShippingAddressStepVisible);
                    },
                    this
                );

                return this;
            },

            /**
             * @inheritdoc
             */
            initObservable: function () {
                this._super();

                this.observe([
                    'isAddressFormVisible',
                    'isAddNewAddressLinkVisible',
                    'isAddressListVisible',
                    'isAddressDetailsVisible',
                    'isCurrentAddressNew',
                    'isVisibleSameAsBillingFlag',
                    'isStepVisible',
                    'isExistingAddressOpenToEdit',
                    'isSaveNewAddressCheckboxVisible',
                    'isFormInline',
                    'shippingAddressForDetails',
                    'isAddressSameAsBilling'
                ]);

                this.isAddressSameAsBilling = ko.pureComputed({
                    read: function () {
                        if (checkoutData.getAddressTypeToDisplayFirst() == 'billing' && !instorePickupState.isStorePickupSelected()) {
                            return sameAsShippingFlag.sameAsBilling();
                        }

                        return false;
                    },
                    write: function (value) {
                        if (checkoutData.getAddressTypeToDisplayFirst() == 'billing') {
                            sameAsShippingFlag.sameAsBilling(value);
                        } else {
                            sameAsShippingFlag.sameAsBilling(false);
                        }
                    },

                    owner: this
                });

                if (this.isFormInline()) {
                    this.isCurrentAddressNew(true);
                }

                this.shippingAddressForDetails = ko.computed(function () {
                    if (this.isAddressSameAsBilling()) {
                        var quoteAddress = checkoutData.getAddressTypeToDisplayFirst() == 'billing'
                                ? quote.billingAddress()
                                : quote.shippingAddress(),
                            addressForDetails = this.isFormInline()
                                ? createShippingAddressAction(
                                    checkoutData.getBillingAddressFromData()
                                    || window.checkoutConfig.billingAddressFromData
                                )
                                : quoteAddress

                        currentShippingAddress.address(addressForDetails);

                        return addressForDetails;
                    }

                    return false;
                }, this),

                    this.isAddressDetailsVisible = ko.computed(function () {
                        if (!this.isAddressSameAsBilling()) {
                            return false;
                        }

                        return !addressFormState.isShippingNewFormOpened()
                            && ((!customer.isLoggedIn() && (this.isAddressSameAsBilling() || this.isQuoteVirtual))
                                || customer.isLoggedIn());
                    }, this);

                this.isVisibleSameAsBillingFlag = ko.computed(function () {
                    return checkoutData.getAddressTypeToDisplayFirst() == 'billing';
                }, this),

                    this.isExistingAddressOpenToEdit = ko.computed(function () {
                        var address = currentShippingAddress.address();
                        if (address && !this.isAddressNew(address)) {
                            return !currentShippingAddress.isValid();
                        }

                        return false;
                    }, this);

                this.isSaveNewAddressCheckboxVisible = ko.computed(function () {
                    return customer.isLoggedIn() && this.isCurrentAddressNew();
                }, this);

                // check is shipping address form is opened to input data
                addressFormState.isShippingFormOpened(!sameAsShippingFlag.sameAsBilling() && (this.isFormInline() || this.isAddressFormVisible()));
                this.isAddressFormVisible.subscribe(function (isVisible) {
                    addressFormState.isShippingFormOpened(isVisible)
                }, this);

                sameAsShippingFlag.sameAsBilling.subscribe(function (isShippingSameAsBilling) {
                    addressFormState.isShippingFormOpened(!isShippingSameAsBilling && (this.isFormInline() || this.isAddressFormVisible()))
                }, this);

                if (checkoutData.getAddressTypeToDisplayFirst() == 'shipping') {
                    this.saveInAddressBook(1);
                }

                return this;
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
             * after update billing address country id
             */
            afterCountryUpdate: function (value) {
                if (checkoutData.getAddressTypeToDisplayFirst() == 'billing'
                    && sameAsShippingFlag.sameAsBilling()
                ) {
                    var address = currentShippingAddress.address();
                    if (address) {
                        address.country_id = value;
                        currentShippingAddress.address(address);
                    }
                }

                if (addressFormState.isBillingFormOpened()
                    || addressFormState.isBillingNewFormOpened()
                    || !customer.isLoggedIn()
                ) {
                    var address = currentBillingAddress.address();

                    if (address) {
                        address.country_id = value;
                        quote.billingAddress(address);
                    }
                }
            },

            /**
             * On use billing address checkbox click event handler
             */
            onUseBillingAddress: function () {
                var addressData,
                    newShippingAddress,
                    address;

                if (checkoutData.getAddressTypeToDisplayFirst() == 'billing') {
                    if (this.isAddressSameAsBilling()) {
                        if (this.isFormInline()) {
                            addressData = this.source.get('shippingAddress');
                            this.newAddress = createShippingAddressAction(addressData);
                            addressData = checkoutData.getBillingAddressFromData();
                            address = createShippingAddressAction(addressData);
                        } else {
                            address = currentBillingAddress.address() || quote.billingAddress();
                        }
                        selectShippingAddressAction(address);
                        currentShippingAddress.address(address);
                        checkoutData.setSelectedShippingAddress(address.getKey());
                    } else {
                        if (this.isFormInline()) {
                            addressData = addressConverter.quoteAddressToFormAddressData(this.newAddress);
                            newShippingAddress = this.newAddress;
                            selectShippingAddressAction(newShippingAddress);
                            currentShippingAddress.address(newShippingAddress);
                            currentShippingAddress.isNew(false);
                            checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                            checkoutData.setNewCustomerShippingAddress(addressData);
                        } else {
                            if (quote.shippingAddress()) {
                                currentShippingAddress.address(quote.shippingAddress());
                                checkoutData.setSelectedShippingAddress(quote.shippingAddress().getKey());
                            } else {
                                checkoutData.setSelectedShippingAddress(null);
                            }
                        }
                    }
                }
                return true;
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
             * @param {Object} shippingAddress
             */
            onCurrentAddressChange: function (shippingAddress) {
                var isValid;

                this.fillUpFormWithAddress(shippingAddress);
                this.resetFormValidation();

                isValid = this.isFormValid();
                if (this.isAddressNew(shippingAddress)) {
                    currentShippingAddress.isNew(true);
                    this.resetFormValidation();
                } else {
                    currentShippingAddress.isNew(false);
                    addressFormState.isShippingNewFormOpened(!isValid);
                }

                currentShippingAddress.isValid(isValid);
                if (isValid) {
                    selectShippingAddressAction(currentShippingAddress.address());
                    checkoutData.setSelectedShippingAddress(currentShippingAddress.address().getKey());
                    if (!quote.isQuoteVirtual()
                        && sameAsShippingFlag.sameAsShipping()
                        && checkoutData.getAddressTypeToDisplayFirst() == 'shipping'
                    ) {
                        selectBillingAddressAction(quote.shippingAddress());
                        addressFormState.isBillingNewFormOpened(false);
                    }
                }
                this.isAddressListVisible(isValid);
            },

            /**
             * On save new address button click event handler
             */
            onSaveNewAddressClick: function () {
                var addressData,
                    newShippingAddress;

                if (this.isFormValid()) {
                    addressData = this.source.get(this.scopeId);
                    addressData['save_in_address_book'] = this.saveInAddressBook() ? 1 : 0;
                    addressData['telephone'] = addressData['telephone'].trim();
                    this._closeNewShippingAddressForm();

                    newShippingAddress = createShippingAddressAction(addressData);
                    this.newAddress = newShippingAddress;
                    selectShippingAddressAction(newShippingAddress);
                    checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                    checkoutData.setNewCustomerShippingAddress(addressData);
                    currentShippingAddress.address(newShippingAddress);
                    this.isAddNewAddressLinkVisible(false);
                }
            },

            /**
             * On update address button click event handler
             */
            onUpdateAddressClick: function () {
                var currentAddress, newAddress, addressFormData;

                if (this.isFormValid()) {
                    addressFormData = this.source.get(this.scopeId);
                    newAddress = addressConverter.formAddressDataToQuoteAddress(addressFormData);
                    currentAddress = currentShippingAddress.address();
                    if (currentAddress) {
                        this.mergeAdress(currentAddress, newAddress);
                        currentShippingAddress.address(currentAddress);
                        updateCustomerAddress(currentAddress);
                    } else {
                        currentShippingAddress.address(newAddress);
                    }

                    this.isAddressListVisible(true);
                }
            },

            /**
             * On add new address click event handler
             */
            onAddNewAddressClick: function () {
                var currentAddress = currentShippingAddress.address();
                currentShippingAddress.previousAddress = currentAddress || quote.shippingAddress();

                if (!currentAddress || currentAddress.getKey() !== this.newAddress.getKey()) {
                    currentShippingAddress.address(this.newAddress);
                }

                if (!addressFormState.isShippingNewFormOpened()) {
                    addressFormState.isShippingNewFormOpened(true);
                    this.isAddressListVisible(false);
                }
            },

            /**
             * On cancel button click event event handler
             */
            onCancelClick: function () {
                currentShippingAddress.address(currentShippingAddress.getPreviousAddress());
                this._closeNewShippingAddressForm();
            },

            /**
             * Check if use form data
             *
             * @returns {boolean}
             */
            useFormData: function () {
                return this.isStepVisible()
                    && (this.isAddressFormVisible() || this.isFormInline());
            },

            /**
             * @inheritdoc
             */
            validate: function () {
                var address;

                if (this.isAddressSameAsBilling()) {
                    this.onUseBillingAddress();
                }

                if (this.isStepVisible()) {
                    if (this.isFormInline() || this.isAddressFormVisible()) {
                        this._super();
                    } else {
                        address = currentShippingAddress.address() || quote.shippingAddress();
                        if (address) {
                            this.fillUpFormWithAddress(address);
                            this.resetFormValidation();
                            if (!this.isFormValid()) {
                                currentShippingAddress.address(address);
                            }
                        }
                    }
                }
            },

            /**
             * Prepare new shipping address
             */
            prepareNewShippingAddress: function () {
                var newAddressFormData = this._getCheckoutAddressFormData()
                    ? this._getCheckoutAddressFormData() : this.source.get(this.scopeId);

                this.newAddress = addressConverter.formAddressDataToQuoteAddress(newAddressFormData);
            },

            /**
             * Check Enable Amazon Pay
             */
            amazonPayIsDefined: function() {
                return uiRegistry.get('amazonPay');
            },

            /**
             * @return {Boolean}
             */
            validateShippingInformation: function () {
                return true;
            },

            /**
             * Open new address form
             */
            _openNewShippingAddressForm: function () {
                this.isAddressFormVisible(true);
                this._setValueShippingAddressFormVisible(true);
                this.isAddressListVisible(false);
                addressFormState.isShippingNewFormOpened(true);
            },

            /**
             * Close new address form
             */
            _closeNewShippingAddressForm: function () {
                this.isAddressFormVisible(false);
                this._setValueShippingAddressFormVisible(false);
                this.isAddressListVisible(true);
                addressFormState.isShippingNewFormOpened(false);
            },

            /**
             * @inheritdoc
             */
            _getCheckoutAddressFormData: function () {
                return checkoutData.getShippingAddressFromData();
            },

            /**
             * @inheritdoc
             */
            _setCheckoutAddressFormData: function (addressData) {
                var shippingAddressComponent = uiRegistry.get('index = shippingAddress');
                this._super();
                if (shippingAddressComponent && !addressData.hasOwnProperty('save_in_address_book')) {
                    addressData.save_in_address_book = shippingAddressComponent.saveInAddressBook() ? 1 : 0
                }
                checkoutData.setShippingAddressFromData(addressData);
            },

            /**
             * @inheritdoc
             */
            _resolveAddress: function () {
                checkoutDataResolver.resolveShippingAddress();
            },

            /**
             * @inheritdoc
             */
            _afterSetInitialAddressFormData: function () {
                uiRegistry.async(this.name + '.shipping-address-fieldset')(function (fieldSet) {
                    if (fieldSet.elems().length > 0) {
                        _.each(fieldSet.elems(), function (fieldRow) {
                            shippingRatesValidator.initFields(fieldRow.name);
                        });
                    }
                    fieldSet.elems.subscribe(function (FieldSetElems) {
                        _.each(FieldSetElems, function (fieldRow) {
                            shippingRatesValidator.initFields(fieldRow.name);
                        });
                    });
                });
            }
        });
    }
);
