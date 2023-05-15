define([
    'uiComponent',
    'mage/translate'
], function (Component, $t) {
    'use strict';

    var deliveryDateConfig = window.checkoutConfig.deliveryDate.note;

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/delivery-date/note'
        },

        /**
         * Check if block enabled
         *
         * @returns {boolean}
         */
        isEnabled: function () {
            return deliveryDateConfig.isEnabled && deliveryDateConfig.text.length;
        },

        /**
         * Get note
         *
         * @returns {string}
         */
        getText: function () {
            return $t(deliveryDateConfig.text);
        }
    });
});
