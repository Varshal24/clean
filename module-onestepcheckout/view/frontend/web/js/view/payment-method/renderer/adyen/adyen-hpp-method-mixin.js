define(
    [
        'underscore'
    ],
    function (_) {
        'use strict';

        return function (renderer) {
            return renderer.extend({

                /**
                 * Fix of the error when the street address is not specified on the checkout page
                 * @returns {{country: (string|*), firstName: (string|*), lastName: (string|*), city: (*|string), street: *, postalCode: (*|string), houseNumber: string, telephone: (string|*)}}
                 */
                getFormattedAddress: function(address) {
                    if (_.isUndefined(address.street)) {
                        address.street = [];
                    }

                    return this._super(address);
                },

                /**
                 * Adyen library v7/8 compatibility bug fix
                 */
                getAdyenHppPaymentMethods: function(paymentMethodsResponse) {
                    var paymentMethods = this._super();

                    return _.map(paymentMethods, function(paymentMethod){
                        if (_.isUndefined(paymentMethod.payment)) {
                            paymentMethod.payment = paymentMethod.item;
                        }

                        return paymentMethod;
                    });
                },
            });
        }
    }
);
