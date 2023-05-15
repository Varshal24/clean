define([
    'jquery',
    'ko',
    'Amazon_Payment/js/model/storage',
    'Aheadworks_OneStepCheckout/js/view/shipping-address',
    'Magento_Customer/js/model/address-list'
], function ($, ko, amazonStorage, Component, addressList) {
    'use strict';

    return Component.extend({
        /**
         * @inheritdoc
         */
        initialize: function() {
            this._super();

            if (amazonStorage.isAmazonAccountLoggedIn()) {
                this.isFormInline(false);
                this.isAddNewAddressLinkVisible(false);
            }

            amazonStorage.isAmazonAccountLoggedIn.subscribe(function (value) {
                if (value) {
                    this.isFormInline(false);
                    this.isAddNewAddressLinkVisible(false);
                } else {
                    this.isFormInline(addressList().length == 0);
                    this.isAddNewAddressLinkVisible(true);
                }
            }, this);

            return this;
        }
    });
});
