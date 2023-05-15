define([
    'Magento_CheckoutAgreements/js/view/checkout-agreements'
], function (Component) {
    'use strict';

    return Component.extend({

        /**
         * @inheritdoc
         */
        getCheckboxId: function (context, agreementId) {
            var paymentMethodName = '',
                paymentMethodRenderer = context.$parent,
                checkboxId = 'agreement_' + agreementId;

            if (paymentMethodRenderer) {
                paymentMethodName = paymentMethodRenderer.item ? paymentMethodRenderer.item.method : '';
                checkboxId = 'agreement_' + paymentMethodName + '_' + agreementId;
            }

            return checkboxId;
        }
    });
});
