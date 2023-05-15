define(
    [
        'Aheadworks_OneStepCheckout/js/view/form/element/validation-enabled-flag',
        'jquery',
        'Magento_Ui/js/lib/view/utils/async'
    ],
    function (validationEnabledFlag, $) {
        'use strict';

        return function (component) {
            return component.extend({

                /**
                 * @inheritdoc
                 */
                initialize: function () {
                    this._super();
                    if (window.checkoutConfig) {
                        $.async('#' + this.uid, function (element) {
                            $(element).trigger('awOscForceRefresh');
                        });
                    }

                    return this;
                },

                /**
                 * @inheritdoc
                 */
                validate: function () {
                    if (!window.checkoutConfig) {
                        return this._super();
                    }
                    if (!this.validationParams) {
                        this.validationParams = {
                            dateFormat: 'MM/dd/Y'
                        }
                    }

                    return validationEnabledFlag()
                        ? this._super()
                        : {valid: true, target: this};
                }
            });
        }
    }
);
