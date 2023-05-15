define([
    'require',
    'ko'
], function($, ko) {
    'use strict';

    var mixin = {
            defaults: {
                template: 'Wexo_Shipping/shipping-method'
            }
        };

    return function (target) {
        return target.extend(mixin);
    };
});