define(
    [
        'underscore',
        'uiComponent',
        'uiLayout',
        'mageUtils',
        'Magento_Checkout/js/model/quote',
        'uiRegistry'
    ],
    function (_, Component, layout, utils, quote, registry) {
        'use strict';

        var defaultRendererComponent = 'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default';

        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/actions-toolbar',
                methodCode: null,
                isRenderButtonDefault: false,
                rendererList: {},
                rendererButton: {},
                currentRendererComponent: null,
                currentMethodCode: null
            },

            /**
             * @inheritdoc
             */
            initialize: function () {
                this._super();

                if (!this.isRenderButtonDefault) {
                    this._createActionRenderer();

                    quote.paymentMethod.subscribe(function (value) {
                        if (value) {
                            this.methodCode(value.method);
                        } else {
                            this.methodCode(null);
                        }
                        this.reInitActionRenderer();
                    }, this);
                }

                return this;
            },

            /**
             * @inheritdoc
             */
            initObservable: function () {
                this._super()
                    .observe({
                        'methodCode': quote.paymentMethod()
                            ? quote.paymentMethod().method
                            : null
                    });

                return this;
            },

            /**
             * Reinitialize action renderer
             */
            reInitActionRenderer: function () {
                if (this.currentRendererComponent != this._getRendererComponent()) {
                    this._removeActionRender();
                    this._createActionRenderer();
                } else {
                    this._reAssignActionRenderer();
                }
            },

            /**
             * Get current renderer component
             *
             * @returns {string}
             */
            _getRendererComponent: function () {
                var methodCode = this.methodCode();

                if (methodCode) {
                    methodCode = methodCode.replace(/vault_(\d+)/g, 'vault');
                    return typeof this.rendererList[methodCode] != 'undefined'
                        ? this.rendererList[methodCode]
                        : defaultRendererComponent;
                } else {
                    return defaultRendererComponent
                }
            },

            /**
             * Create action renderer
             */
            _createActionRenderer: function () {
                var methodCode = this.methodCode(),
                    component = this._getRendererComponent(),
                    rendererTemplate = {
                        parent: '${ $.$data.parentName }',
                        name: '${ $.$data.name }',
                        displayArea: '${ $.$data.displayArea }',
                        component: component,
                        methodCode: methodCode,
                        isPlaceOrderBtnVisible: typeof this.rendererButton[methodCode] != 'undefined'
                            ? this.rendererButton[methodCode]
                            : true
                    },
                    templateData = {
                        parentName: this.name,
                        name: methodCode + '_actions',
                        displayArea: 'payment-actions'
                    },
                    rendererComponent = utils.template(rendererTemplate, templateData);

                registry.async('checkout.actions-toolbar.' + this.currentMethodCode + '_actions')(
                    function (component) {
                        if (component.component !== this.currentRendererComponent) {
                            component.isPlaceOrderBtnVisible(false);
                        }
                    }.bind(this)
                );

                layout([rendererComponent]);
                this.currentRendererComponent = component;
                this.currentMethodCode = methodCode;
            },

            /**
             * Remove action renderer
             */
            _removeActionRender: function () {
                var items = this.getRegion('payment-actions');

                _.find(items(), function (value) {
                    value.disposeSubscriptions();
                    value.destroy();
                });
            },

            /**
             * Reassign action renderer component to new payment method component
             */
            _reAssignActionRenderer: function () {
                var self = this,
                    items = this.getRegion('payment-actions');

                _.find(items(), function (value) {
                    var isPlaceOrderBtnVisible = typeof self.rendererButton[self.methodCode()] != 'undefined'
                        ? self.rendererButton[self.methodCode()]
                        : true;

                    value.isPlaceOrderBtnVisible(isPlaceOrderBtnVisible);
                    value.methodCode = self.methodCode();
                    value.initMethodsRenderComponent();
                });
            }
        });
    }
);
