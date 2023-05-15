define(
    [
        'jquery',
        'mage/utils/wrapper'
    ],
    function ($, wrapper) {
        'use strict';

        return function (addressData) {
            return wrapper.wrap(addressData, function (originalAction, addressData) {
                var result = originalAction(addressData);
                result.postcode = addressData.postcode;

                return result;
            });
        };
    }
);
