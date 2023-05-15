define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default',
    'Magento_Checkout/js/model/full-screen-loader',
    'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-validator',
    'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-checkout-data',
    'Magento_Checkout/js/model/quote',
    'mage/translate'
], function (
    $,
    _,
    registry,
    Component,
    fullScreenLoader,
    aggregateValidator,
    aggregateCheckoutData,
    quote,
    $t
) {
    'use strict';

    return Component.extend({

        /**
         * @inheritdoc
         */
        _getMethodRenderComponent: function () {
            if (!this.methodRendererComponent) {
                this.initMethodsRenderComponent();
            }

            this._initChildMethodRendererComponent();

            return this.methodRendererComponent;
        },

        /**
         * @inheritdoc
         */
        _initChildMethodRendererComponent: function () {
            var childComponents = this.methodRendererComponent.adyenPaymentMethods(),
                selectedPaymentMethod = this.methodRendererComponent.getSelectedAlternativePaymentMethodType(),
                childComponent;

            childComponent = _.find(childComponents, function(component){
                return component.paymentMethod.type == selectedPaymentMethod
            });

            if (!_.isUndefined(childComponent)) {
                this.methodRendererComponent = childComponent;
            }
        },

        /**
         * @inheritdoc
         */
        placeOrder: function () {
            var self = this;

            this._beforeAction().done(function () {
                self._getMethodRenderComponent().placeOrder();
            });
        }
    });
});
