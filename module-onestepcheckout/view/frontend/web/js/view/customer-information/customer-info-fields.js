define([
    'Magento_Ui/js/form/form',
    'Aheadworks_OneStepCheckout/js/model/customer-information/customer-info-form-state',
    'Aheadworks_OneStepCheckout/js/model/checkout-data-completeness-logger',
    'uiRegistry'
], function (Component, formState, completenessLogger, uiRegistry) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/customer-information/customer-info-fields'
        },
        isCustomerInfoSectionDisplayed: checkoutConfig.isCustomerInfoSectionDisplayed,
        isFormVisible: formState.isVisible,

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super();
            this.initCustomerInfoFormState();

            return this;
        },

        /**
         * Initialized customer info form state
         */
        initCustomerInfoFormState: function () {
            uiRegistry.async(this.name + '.customer-info-fieldset')(function (fieldSet) {
                this.isFormVisible(this.isCustomerInfoSectionDisplayed && fieldSet.elems().length > 0);
                completenessLogger.bindCustomerInfoFieldsData(fieldSet.source);
            }.bind(this));
        },

        /**
         * Validate fields
         */
        validate: function () {
            this.source.set('params.invalid', false);
            this.source.trigger('customerInfo.data.validate');
        },
    });
});
