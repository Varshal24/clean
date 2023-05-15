define([
    'uiRegistry',
    'underscore',
    'mage/utils/objects',
    'mage/utils/misc'
], function (registry, _, objectsUtils, miscUtils) {
    'use strict';

    return {

        /**
         * Get customer information data
         *
         * @returns {Object}
         */
        getData: function () {
            var provider = registry.get('checkoutProvider'),
                customerInfo = objectsUtils.copy(provider.get('customerInfo'));

            customerInfo = miscUtils.filterFormData(customerInfo);
            if (customerInfo['custom_attributes']) {
                customerInfo['custom_attributes'] = _.map(
                    customerInfo['custom_attributes'],
                    function (value, key) {
                        return {
                            'attribute_code': key,
                            'value': Array.isArray(value) ? value.join(',') : value
                        };
                    }
                );
            }

            return customerInfo;
        }
    };
});