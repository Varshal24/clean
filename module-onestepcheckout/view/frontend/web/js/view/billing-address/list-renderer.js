define([
    'Aheadworks_OneStepCheckout/js/view/abstract-address/list-renderer',
    './list',
], function (ListRenderer, addressList) {
    'use strict';

    return ListRenderer.extend({
        defaults: {
            isShown: addressList().length > 0,
            renders: []
        },

        /**
         * @inheritdoc
         */
        getAddressList: function () {
            return addressList;
        },

        /**
         * @inheritdoc
         */
        getDefaultRendererTemplate: function () {
            var template = this._super();

            template['component'] = 'Aheadworks_OneStepCheckout/js/view/billing-address/address-renderer/default';
            return template;
        },
    });
});
