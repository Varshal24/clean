define([
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/actions-toolbar/renderer/default'
        },

        /**
         * Place order
         *
         * @param {Object} data
         * @param {Object} event
         */
        placeOrder: function (data, event) {
            var self = this;

            if (event) {
                event.preventDefault();
            }

            this._beforeAction().done(function () {
                self._getMethodRenderComponent().onPlaceOrder();
            });
        },
    });
});