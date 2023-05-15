define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/customer-data',
    'Aheadworks_OneStepCheckout/js/model/address-form-state',
    'Aheadworks_OneStepCheckout/js/model/billing-address/current-address',
    'Aheadworks_OneStepCheckout/js/model/checkout-data-completeness-logger',
    'Aheadworks_OneStepCheckout/js/model/checkout-section/service-busy-flag',
    'Aheadworks_OneStepCheckout/js/model/address-list-service',
    'Aheadworks_OneStepCheckout/js/view/street-resolver',
    'Magento_Checkout/js/model/address-converter'
], function (
    ko,
    Component,
    quote,
    customerData,
    addressFormState,
    currentBillingAddress,
    completenessLogger,
    serviceBusyFlag,
    addressListService,
    streetResolver,
    addressConverter
) {
    'use strict';

    var countryData = customerData.get('directory-data');

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/abstract-address/address-renderer/default',
            selectActionText: 'Select'
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            completenessLogger.bindSelectedAddressData('billingAddress', quote.billingAddress);
            serviceBusyFlag.subscribe(function (newValue) {
                addressListService.isLoading(newValue);
            }, this);
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super();

            this.address = ko.observable(this.address());

            this.isSelected = ko.computed(function() {
                var isSelected = false,
                    billingAddress = quote.billingAddress();

                if (billingAddress) {
                    isSelected = billingAddress.getKey() == this.address().getKey();
                }

                return isSelected;
            }, this);

            quote.billingAddress.subscribe(function () {
                if (this.isSelected() && !currentBillingAddress.address()) {
                    this.onSelectAddressClick();
                }
            }.bind(this));

            currentBillingAddress.address.subscribe(function (billingAddress) {
                if (billingAddress.getKey() == this.address().getKey()) {
                    this.address(billingAddress);
                }
            }, this);

            return this;
        },

        /**
         * Get country name
         *
         * @param {string} countryId
         * @returns {string}
         */
        getCountryName: function(countryId) {
            return (countryData()[countryId] != undefined) ? countryData()[countryId].name : '';
        },

        /**
         * On select address click event handler
         */
        onSelectAddressClick: function() {
            if (!serviceBusyFlag()) {
                currentBillingAddress.previousAddress = currentBillingAddress.address();
                currentBillingAddress.address(this.address());
            }
        },

        /**
         * On edit address click event handler
         */
        onEditAddressClick: function() {
            currentBillingAddress.previousAddress = currentBillingAddress.address();
            currentBillingAddress.address(this.address());
            addressFormState.isBillingNewFormOpened(true);
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
});
