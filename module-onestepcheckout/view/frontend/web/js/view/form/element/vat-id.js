define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'Magento_Ui/js/lib/validation/validator',
    'mage/translate',
    'Magento_Checkout/js/model/full-screen-loader'
], function ($, _, Abstract, validation, $t, fullScreenLoader) {
    'use strict';

    validation.addRule('vat-validation', (value, params, component) => {
        var result = false;

        if (component.value()) {
            result = component.validateVat();
        }

        return result;
    }, $t('Error during VAT Number verification.'));

    return Abstract.extend({
        defaults: {
            cacheableNumbers: {},
            ajaxValidations: {
                'vat-validation': '1'
            },
            listens: {
                '${ $.provider }:${ $.customScope }.country_id:value': 'afterCountryUpdate'
            }
        },

        /**
         * Initializes component
         */
        initialize: function () {
            this._super();
            this.validationParams = this;

            return this;
        },

        /**
         * Callback that fires when 'value' property is updated.
         */
        onUpdate: function () {
            this.bubble('update', this.hasChanged());

            if (this.value()) {
                this.validate();
            }
        },

        /**
         * Validate vat number
         */
        validateVat: function () {
            if (this.checkIsNeedAjaxValidation()) {
                var cacheableNumber = this.checkCacheableNumbers(),
                    result,
                    value = this.value(),
                    countryId = this.country_id;

                if (cacheableNumber === null) {
                    fullScreenLoader.startLoader();

                    $.ajax({
                        url: window.checkoutConfig.vatValidationUrl,
                        data: {
                            'country_id': countryId,
                            'vat_number': value
                        },
                        dataType: 'json',
                        type: 'POST',
                        async: false,
                        cache: true,

                        /**
                         * success callback.
                         */
                        success: (response) => {
                            response.is_valid ? result = true : result = false;

                            if (this.cacheableNumbers[countryId]) {
                                _.extend(this.cacheableNumbers[countryId], {
                                    [value]: result
                                    });
                            } else {
                                this.cacheableNumbers[countryId] = {
                                    [value]: result
                                }
                            }
                        },

                        /**
                         * error callback.
                         */
                        error: () => {
                            result = false;
                        }
                    });
                    fullScreenLoader.stopLoader();
                } else {
                    result = cacheableNumber;
                }

                return result;
            }

            return true;
        },

        /**
         * Check if the number has already been verified
         */
        checkCacheableNumbers: function () {
            var result = null,
                self = this;

            _.each(this.cacheableNumbers, function (elem, countryId) {
                if (countryId === self.country_id && elem[self.value()] !== undefined) {
                    result = elem[self.value()];
                }
            });

            return result;
        },

        /**
         * After country update
         */
        afterCountryUpdate: function (value) {
            this.country_id = value;

            if (this.value()) {
                this.validate();
            }
        },

        /**
         * Check is need ajax validation before native validation
         */
        checkIsNeedAjaxValidation: function () {
            return _.intersection(
                Object.keys(this.validation),
                Object.keys(this.ajaxValidations)
            ).length
        }
    });
});
