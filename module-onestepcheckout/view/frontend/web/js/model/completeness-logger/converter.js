define(
    [
        'underscore',
        'Magento_Customer/js/model/customer'
    ],
    function (_, customer) {
        'use strict';

        return {
            /**
             * Convert form fields log data into field completeness data
             *
             * @param {Object} logData
             * @returns {Array}
             */
            convertToFieldCompletenessData: function (logData) {
                var result = [];

                _.each(logData, function (value, key) {
                    if (_.isFunction(value)) {
                        value = value();
                    }
                    if (key == 'email' && customer.isLoggedIn()) {
                        value = true;
                    }
                    if (!this._isFormData(key)) {
                        result.push({
                            'field_name': key,
                            'is_completed': value,
                            'scope': null
                        });
                    }
                }, this);

                return _.union(
                    result,
                    this._getCustomerInfoData(logData),
                    this._getAddressData(logData, 'shippingAddress'),
                    this._getAddressData(logData, 'billingAddress')
                );
            },

            /**
             * Check if form data
             *
             * @param {string} key
             * @returns {boolean}
             */
            _isFormData: function (key) {
                var addressKeys = [
                    'customerInfo',
                    'shippingAddressData',
                    'billingAddressData',
                    'shippingAddressSelected',
                    'billingAddressSelected'
                ];

                return _.indexOf(addressKeys, key) != -1;
            },

            /**
             * Retrieves customer info data
             *
             * @param {Object} logData
             */
            _getCustomerInfoData: function (logData) {
                var result = [],
                    formData = logData['customerInfo'];

                _.each(formData, function (value, fieldName) {
                    result.push({
                        'field_name': fieldName,
                        'is_completed': value,
                        'scope': 'customerInfo'
                    });
                });

                return result;
            },

            /**
             * Retrieves address data
             *
             * @param {Object} logData
             * @param {string} addressType
             */
            _getAddressData: function (logData, addressType) {
                var result = [],
                    formData = logData[addressType + 'Data'],
                    selectedAddressData = logData[addressType + 'Selected'],
                    addressData;

                if (customer.isLoggedIn()) {
                    addressData = selectedAddressData || formData;
                } else {
                    addressData = formData;
                }
                addressData = addressData || {};

                if (addressData['region'] !== undefined) {
                    addressData.region = addressData.region === true
                        || addressData.region_id === true;
                    delete addressData.region_id;
                }

                _.each(addressData, function (value, fieldName) {
                    if (_.isObject(value)) {
                        _.each(value, function (item, index) {
                            result.push({
                                'field_name': fieldName + '_line_' + index,
                                'is_completed': item,
                                'scope': addressType
                            });
                        });
                    } else {
                        result.push({
                            'field_name': fieldName,
                            'is_completed': value,
                            'scope': addressType
                        });
                    }
                });

                return result;
            }
        };
    }
);
