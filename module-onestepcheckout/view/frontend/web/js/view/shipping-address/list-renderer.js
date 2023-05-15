define([
    'Aheadworks_OneStepCheckout/js/view/abstract-address/list-renderer',
], function (ListRenderer) {
    'use strict';

    return ListRenderer.extend({
        defaults: {
            renders: []
        },

        /**
         * @inheritdoc
         */
        getDefaultRendererTemplate: function () {
            var template = this._super();

            template['component'] = 'Aheadworks_OneStepCheckout/js/view/shipping-address/address-renderer/default';
            return template;
        },
    });
});
