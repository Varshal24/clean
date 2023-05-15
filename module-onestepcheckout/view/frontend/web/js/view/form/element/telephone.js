define([
    'Magento_Ui/js/form/element/abstract',
    'Magento_Ui/js/lib/validation/validator',
    'mage/translate',
], function (Abstract,validation, $t) {
    'use strict';

    return Abstract.extend({

        /**
         * @inheritdoc
         */
        initialize: function () {
            validation.addRule('aw-osc-validate-telephone', function (value) {
                if (value !== '') {
                    return (/^([+]?[\s0-9]+)?(\d{3}|[(]?[0-9]+[)])?([-]?[\s]?[0-9])+$/).test(value.trim());
                }

                return true;
            }, $t('Please enter a valid phone number. It can contain only numbers, +, -, whitespace and ()'));

            this._super();
        },
    });
});
