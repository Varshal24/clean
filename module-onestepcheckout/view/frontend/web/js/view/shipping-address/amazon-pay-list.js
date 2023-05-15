define([
    'Aheadworks_OneStepCheckout/js/view/shipping-address/list-renderer',
    'Magento_Customer/js/model/address-list',
    'Amazon_Pay/js/model/storage',
    'ko'
], function (Component, addressList, amazonStorage, ko) {
    'use strict';

    return Component.extend({
        isAmazonCheckout: amazonStorage.isAmazonCheckout(),
        /**
         * Init address list
         */
        initObservable: function () {
            this._super().observe('isShown');
            if (amazonStorage.isAmazonCheckout()) {
                this.rendererTemplates = {
                    'new-customer-address': {
                        component: 'Aheadworks_OneStepCheckout/js/view/shipping-address/address-renderer/default',
                        template: 'Aheadworks_OneStepCheckout/shipping-address/address-renderer/amazon-pay'
                    }
                }

                this.isShown(true);
            }

            return this;
        },

        _createRenderer: function (address, index) {
            if (address.getType() === 'new-customer-address' || !this.isAmazonCheckout) {
                // Only display one address from Amazon
                return this._super();
            }
        }
    });
});