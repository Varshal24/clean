define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/customer-data',
    'Aheadworks_OneStepCheckout/js/model/address-form-state',
    'Aheadworks_OneStepCheckout/js/model/shipping-address/current-address',
    'Aheadworks_OneStepCheckout/js/model/checkout-data-completeness-logger',
    'Aheadworks_OneStepCheckout/js/model/checkout-section/service-busy-flag',
    'Aheadworks_OneStepCheckout/js/model/address-list-service',
    'Aheadworks_OneStepCheckout/js/view/street-resolver'
], function (
    ko,
    Component,
    quote,
    customerData,
    addressFormState,
    currentShippingAddress,
    completenessLogger,
    serviceBusyFlag,
    addressListService,
    streetResolver
) {
    'use strict';

    var countryData = customerData.get('directory-data');

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/abstract-address/address-renderer/default',
            selectActionText: 'Ship Here'
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            completenessLogger.bindSelectedAddressData('shippingAddress', quote.shippingAddress);
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
                    shippingAddress = quote.shippingAddress();

                if (shippingAddress) {
                    isSelected = shippingAddress.getKey() == this.address().getKey();
                }

                return isSelected;
            }, this);

            currentShippingAddress.address.subscribe(function (shippingAddress) {
                if (shippingAddress.getKey() == this.address().getKey()) {
                    this.address(shippingAddress);
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
                currentShippingAddress.previousAddress = currentShippingAddress.address();
                currentShippingAddress.address(this.address());
            }
        },

        /**
         * On edit address click event handler
         */
        onEditAddressClick: function() {
            currentShippingAddress.previousAddress = currentShippingAddress.address();
            currentShippingAddress.address(this.address());
            addressFormState.isShippingNewFormOpened(true);
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
