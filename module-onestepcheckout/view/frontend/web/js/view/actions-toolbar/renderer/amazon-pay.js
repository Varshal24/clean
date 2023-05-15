define([
    'jquery',
    'ko',
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default',
    'Amazon_Pay/js/model/storage',
    'Aheadworks_OneStepCheckout/js/model/render-postprocessor',
    'Magento_Ui/js/lib/view/utils/async'
], function ($, ko, Component, amazonStorage, postprocessor) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/actions-toolbar/renderer/amazon-pay'
        },

        /**
         * Place Order Button Visible
         */
        isPlaceOrderButtonVisible: function () {
            return amazonStorage.isAmazonCheckout() && !postprocessor.isRenderButtonDefault();
        }
    });
});
