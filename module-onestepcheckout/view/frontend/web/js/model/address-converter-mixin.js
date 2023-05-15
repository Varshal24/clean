define([
        'underscore',
        'mage/utils/wrapper',
    ], function (_, wrapper) {
        'use strict';

        return function (model) {
            return _.extend(model, {

                /**
                 * @inheritdoc
                 */
                formAddressDataToQuoteAddress: wrapper.wrap(
                    model.formAddressDataToQuoteAddress, function (origAction, formData) {
                        var resultAddress = origAction(formData),
                            preparedCustomAttributes = [],
                            attributeData;

                        _.each(resultAddress.customAttributes, function (value, code) {
                            if (!_.isObject(value) || _.isArray(value)) {
                                attributeData = {
                                    attribute_code: code,
                                    value: value
                                }
                            } else {
                                attributeData = value;
                            }
                            preparedCustomAttributes.push(attributeData);
                        });
                        resultAddress.customAttributes = preparedCustomAttributes;

                        return resultAddress;
                    }),

                /**
                 * @inheritdoc
                 */
                quoteAddressToFormAddressData: wrapper.wrap(
                    model.quoteAddressToFormAddressData, function (origAction, addressObject) {
                        var resultFormData = origAction(addressObject),
                            prepareCustomAttributesObject = {};

                        if (_.isArray(addressObject.customAttributes)) {
                            _.each(addressObject.customAttributes, function (attribute) {
                                prepareCustomAttributesObject[attribute.attribute_code] = attribute.value;
                            });

                            resultFormData.custom_attributes = prepareCustomAttributesObject;
                        }

                        return resultFormData;
                    })
            });
        }
    }
);
