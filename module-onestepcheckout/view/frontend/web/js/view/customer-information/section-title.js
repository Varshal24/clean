define([
    'uiComponent',
    'Magento_Customer/js/model/customer',
    'Aheadworks_OneStepCheckout/js/model/customer-information/customer-info-form-state',
    'mage/translate'
], function(Component, customer, formState, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/customer-information/section-title'
        },
        isCustomerLoggedIn: customer.isLoggedIn,

        /**
         * Check if section is visible
         *
         * @return {Boolean}
         */
        isVisible: function() {
            return formState.isVisible() || !this.isCustomerLoggedIn();
        },

        /**
         * Get section title
         *
         * @return {String}
         */
        getTitle: function() {
            return formState.isVisible()
                ? $t('Customer information')
                : $t('Email');
        }
    });
});
