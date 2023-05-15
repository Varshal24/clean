define([
    'underscore',
    'ko',
    'mageUtils',
    'uiComponent',
    'uiLayout',
    'Magento_Customer/js/model/address-list',
    'Aheadworks_OneStepCheckout/js/model/address-list-service',
    'Magento_Checkout/js/model/address-converter'
], function (_, ko, utils, Component, layout, addressList, addressListService, addressConverter) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/abstract-address/list-renderer',
            isShown: addressList().length > 0,
            rendererTemplates: [],
        },
        renders: [],
        isLoading: addressListService.isLoading,

        /**
         * Get list with addresses
         *
         * @return {Object}
         */
        getAddressList: function () {
            return addressList;
        },

        /**
         * Get default renderer template
         *
         * @return {Object}
         */
        getDefaultRendererTemplate: function () {
            return {
                parent: '${ $.$data.parentName }',
                name: '${ $.$data.name }'
            };
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super()
                .initAddressRenders();

            this.getAddressList().subscribe(
                function (changes) {
                    _.each(changes, function (change) {
                        if (change.status === 'added') {
                            this._createRenderer(change.value, change.index);
                        }
                    }, this);
                },
                this,
                'arrayChange'
            );

            return this;
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super();
            this.observe(['isShown']);

            return this;
        },

        /**
         * Init address renders
         *
         * @returns {Component}
         */
        initAddressRenders: function () {
            _.each(this.getAddressList()(), function (address, index) {
                this._createRenderer(address, index);
            }, this);

            return this;
        },

        /**
         * Create address renderer
         *
         * @param address
         * @param index
         */
        _createRenderer: function (address, index) {
            var rendererTemplate,
                templateData,
                defaultRendererTemplate = this.getDefaultRendererTemplate(),
                renderer,
                isNeedRender = true;

            _.each(this.renders, function (render) {
                if (render) {
                    isNeedRender = JSON.stringify(
                        addressConverter.formAddressDataToQuoteAddress(render.address())
                    ) !== JSON.stringify(
                        addressConverter.formAddressDataToQuoteAddress(address)
                    );
                }
            });

            if (isNeedRender) {
                if (index in this.renders) {
                    this.renders[index].address(address);
                } else {
                    if (address.getType() != undefined
                        && this.rendererTemplates[address.getType()] != undefined
                    ) {
                        rendererTemplate = utils.extend(
                            {},
                            defaultRendererTemplate,
                            this.rendererTemplates[address.getType()]
                        );
                    } else {
                        rendererTemplate = defaultRendererTemplate;
                    }
                    templateData = {
                        parentName: this.name,
                        name: index
                    };

                    renderer = utils.template(rendererTemplate, templateData);
                    utils.extend(renderer, {address: ko.observable(address)});
                    layout([renderer]);
                    this.renders[index] = renderer;
                }
            }
        }
    });
});
