define(
    [
        'ko',
        'underscore',
        'Aheadworks_OneStepCheckout/js/model/completeness-logger/converter',
        'Aheadworks_OneStepCheckout/js/action/submit-completeness-log',
        'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/address-converter'
    ],
    function (
        ko,
        _,
        converter,
        submitCompletenessLogAction,
        sameAsShippingFlag,
        quote,
        addressConverter
    ) {
        'use strict';

        var logData = {},
            isLogOfShippingAddressEnabled = ko.computed(function () {
                    return !quote.isQuoteVirtual();
                }),
            isLogOfBillingAddressEnabled = ko.computed(function () {
                return !sameAsShippingFlag.sameAsShipping() || quote.isQuoteVirtual();
            }),
            isStarted = false,
            isEnabledLogging = window.checkoutConfig.isEnabledCheckoutStatistics,
            isLoggingAllowed = ko.observable(true);

        var logger = {

            isLoggingAllowed: isLoggingAllowed,

            /**
             * Bind field
             *
             * @param {string} fieldName
             * @param {Function} observable
             */
            bindField: function (fieldName, observable) {
                var self = this;

                if (logData[fieldName] === undefined) {
                    logData[fieldName] = ko.computed(function () {
                        return self._isCompleted(observable());
                    });
                    logData[fieldName].subscribe(function () {
                        self.submitLog();
                    });
                    self.submitLog();
                }
            },

            /**
             * Bind address fields data
             *
             * @param {string} addressTypeKey
             * @param {Object} dataProvider
             */
            bindAddressFieldsData: function (addressTypeKey, dataProvider) {
                var self = this,
                    key = addressTypeKey + 'Data',
                    addressData = dataProvider.get(addressTypeKey);

                if (logData[key] === undefined) {
                    logData[key] = this._toLogDataFromFormData(addressData);
                    // todo: consider update logData by completed status change but not address data change
                    dataProvider.on(addressTypeKey, function (newAddressData) {
                        logData[key] = self._toLogDataFromFormData(newAddressData);
                        self.submitLog();
                    });
                    self.submitLog();
                }
            },

            /**
             * Bind customer info fields data
             *
             * @param {Object} dataProvider
             */
            bindCustomerInfoFieldsData: function (dataProvider) {
                var self = this,
                    key = 'customerInfo',
                    data = dataProvider.get(key);

                if (logData[key] === undefined) {
                    logData[key] = this._toLogDataFromFormData(data);
                    dataProvider.on(key, function (customerInfoData) {
                        logData[key] = self._toLogDataFromFormData(customerInfoData);
                        self.submitLog();
                    });
                    self.submitLog();
                }
            },

            /**
             * Bind selected address data
             *
             * @param {string} addressTypeKey
             * @param {Function} observable
             */
            bindSelectedAddressData: function (addressTypeKey, observable) {
                var self = this,
                    key = addressTypeKey + 'Selected';

                if (logData[key] === undefined) {
                    logData[key] = this._toLogDataFromFormData(this._convertAddressObjectToFormData(observable()));
                    // todo: consider update logData by completed status change but not address data change
                    observable.subscribe(function (address) {
                        logData[key] = self._toLogDataFromFormData(self._convertAddressObjectToFormData(address));
                        self.submitLog();
                    });
                    self.submitLog();
                }
            },

            /**
             * Convert form data into log data
             *
             * @param {Object} formData
             * @returns {Object}
             * @private
             */
            _toLogDataFromFormData: function (formData) {
                var resultLogData = {};

                _.each(formData, function (value, field) {
                    if (_.isObject(value)) {
                        resultLogData[field] = {};
                        _.each(value, function (item, index) {
                            if (field === 'custom_attributes') {
                                resultLogData[index] = this._isCompleted(item);
                            } else {
                                resultLogData[field][index] = this._isCompleted(item);
                            }
                        }, this);
                    } else {
                        resultLogData[field] = this._isCompleted(value);
                    }
                }, this);

                delete resultLogData['custom_attributes'];
                return resultLogData;
            },

            /**
             * Convert address object to form data
             *
             * @param {Object} address
             * @return {Object}
             */
            _convertAddressObjectToFormData: function (address) {
                if (!address) {
                    return null;
                }
                var formAddressData = addressConverter.quoteAddressToFormAddressData(address);

                return _.omit(formAddressData, function (value) {
                    return value == null;
                });
            },

            /**
             * Check if value completed
             *
             * @param value
             * @returns {boolean}
             */
            _isCompleted: function (value) {
                return value !== undefined && value != '' && value !== null;
            },

            /**
             * Submit log
             *
             * @returns {logger}
             */
            submitLog: function () {
                var buffer = {};

                if (isStarted && isEnabledLogging && this.isLoggingAllowed()) {
                    _.each(logData, function (data, key) {
                        var shippingAddressKeys = ['shippingAddressData', 'shippingAddressSelected'],
                            billingAddressKeys = ['billingAddressData', 'billingAddressSelected'];

                        if (!(_.indexOf(shippingAddressKeys, key) != -1 && !isLogOfShippingAddressEnabled()
                            || _.indexOf(billingAddressKeys, key) != -1 && !isLogOfBillingAddressEnabled())
                        ) {
                            buffer[key] = data;
                        }
                    });
                    submitCompletenessLogAction(
                        converter.convertToFieldCompletenessData(buffer)
                    );
                }

                return this;
            },

            /**
             * Start logging
             */
            start: function () {
                isStarted = true;
                this.submitLog();
            }
        };

        isLogOfShippingAddressEnabled.subscribe(function () {
            logger.submitLog();
        });
        isLogOfBillingAddressEnabled.subscribe(function () {
            logger.submitLog();
        });

        // todo: this is a workaround, should be revised
        //       possible approach is auto trigger when all expected bindings are performed
        window.setTimeout(function () {
            logger.start();
        }, 3000);

        return logger;
    }
);
