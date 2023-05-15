define([
    'jquery',
    'ko',
    'Amazon_Pay/js/model/storage',
    'Aheadworks_OneStepCheckout/js/view/shipping-address',
], function ($, ko, amazonStorage, Component) {
    'use strict';

    return Component.extend({

        /**
         * @inheritdoc
         */
        initialize: function() {
            this._super();

            if (amazonStorage.isAmazonCheckout()) {
                this.isAddNewAddressLinkVisible(false);
                this.isFormInline(false);
            }
            return this;
        }
    });
});
