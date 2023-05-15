define([
    'jquery',
    'ko',
    'Amazon_Pay/js/model/storage',
    'Aheadworks_OneStepCheckout/js/view/billing-address',
    'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag',
    'Aheadworks_OneStepCheckout/js/model/billing-address/current-address',
    'Magento_Checkout/js/model/quote',
    'Amazon_Pay/js/model/billing-address/form-address-state',
    'Aheadworks_OneStepCheckout/js/model/checkout-data',
    'Magento_Checkout/js/action/create-billing-address',
    'Aheadworks_OneStepCheckout/js/view/billing-address/list'
], function (
        $,
        ko,
        amazonStorage,
        Component,
        sameAsShippingFlag,
        currentBillingAddress,
        quote,
        billingFormAddressState,
        checkoutData,
        createBillingAddressAction,
        addressList
    ) {
    'use strict';

    return Component.extend({

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();

            if (amazonStorage.isAmazonCheckout()) {
                this.isAddNewAddressLinkVisible(false);
                this.isFormInline = false;
                this.isStepVisible(false)
            }
            return this;
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super();

            if (amazonStorage.isAmazonCheckout()) {
                this.canUseShippingAddress = ko.computed(function () {
                    return false;
                });

                sameAsShippingFlag.sameAsShipping(this.canUseShippingAddress());
            }
            billingFormAddressState.isLoaded.subscribe(function () {
                this.prepareNewBillingAddress();
            }.bind(this))

            return this;
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
                    address = currentBillingAddress.address() || quote.billingAddress() || quote.shippingAddress();
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
            if (checkoutData.getNewCustomerBillingAddress() && amazonStorage.isAmazonCheckout()) {
                this.addNewBillingAddressToList(newBillingAddress);
            }

            this.newAddress = newBillingAddress;
            this.customerHasAddresses = addressList().length > 0;
        }
    });
});
