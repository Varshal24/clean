define(
    [
        'ko',
        'underscore',
        'Magento_GiftWrapping/js/view/gift-wrapping',
        'Magento_Catalog/js/price-utils',
        'Aheadworks_OneStepCheckout/js/action/update-gift-message',
        'Magento_GiftMessage/js/model/gift-options',
        'Aheadworks_OneStepCheckout/js/model/gift-message-service',
        'Aheadworks_OneStepCheckout/js/model/gift-wrapping-service',
        'Magento_Customer/js/customer-data'
    ],
    function (ko, _, Component, priceUtils, giftMessageAction, giftOptions, giftMessageService, giftWrappingService, customerData) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/sidebar/gift-wrapping',
                appliedTemplate: 'Magento_GiftWrapping/applied'
            },

            /**
             * @inheritdoc
             */
            initialize: function () {
                this._super();
                this._initializeExtraOptions();
                customerData.get("aw-osc-gift-wrapping-info").subscribe(function () {
                    if (this.getAppliedWrappingId()) {
                        this.updateInfoBlock();
                    }
                }, this);
            },

            /**
             * Apply wrapping.
             */
            applyWrapping: function () {
                var wrappingId = this.getAppliedWrappingId(),
                    messageModel = giftOptions.getOptionByItemId(this.levelIdentifier);

                if (wrappingId) {
                    this.model.setActiveItem(wrappingId);
                    this.updateInfoBlock();
                    this._getGiftWrappingData(wrappingId);
                }

                if (messageModel && (this.isExtraOptionsApplied() || wrappingId)) {
                    messageModel.getObservable('additionalOptionsApplied')(true);
                }
            },

            /**
             * Initialize default values for Extra options
             */
            _initializeExtraOptions: function () {
                var giftWrappingConfig = window.giftOptionsConfig ? window.giftOptionsConfig.giftWrapping : false,
                    isGiftReceiptChecked = false,
                    isPrintedCardChecked = false,
                    printedCardPriceInclTax,
                    printedCardPriceExclTax

                if (giftWrappingConfig) {
                    var isPrintedCardChecked = giftWrappingConfig.hasOwnProperty('appliedPrintedCard') ?
                            parseInt(giftWrappingConfig.appliedPrintedCard) : false,
                        isGiftReceiptChecked = giftWrappingConfig.hasOwnProperty('appliedGiftReceipt') ?
                            parseInt(giftWrappingConfig.appliedGiftReceipt) : false,
                        printedCardPriceInclTax = giftWrappingConfig.cardInfo.hasOwnProperty('price_excl_tax') ?
                            giftWrappingConfig.cardInfo.price_excl_tax : giftWrappingConfig.cardInfo.price,
                        printedCardPriceExclTax = giftWrappingConfig.cardInfo.hasOwnProperty('price_incl_tax') ?
                            giftWrappingConfig.cardInfo.price_excl_tax : giftWrappingConfig.cardInfo.price;
                }
                this.getObservable('printedCard')(isPrintedCardChecked);
                this.getObservable('giftReceipt')(isGiftReceiptChecked);
                this.getObservable('printedCardPriceInclTax')(printedCardPriceInclTax);
                this.getObservable('printedCardPriceExclTax')(printedCardPriceExclTax);
            },

            /**
             * Set active item
             *
             * @param {int} id - Selected wrapper ID
             */
            setActiveItem: function (id) {
                this._super(id);
                var params = this._getGiftWrappingData(id);
                giftMessageAction(this._getActionItemId(), params);
            },

            /**
             * Uncheck wrapping.
             */
            uncheckWrapping: function () {
                this._super();
                var params = this._getGiftWrappingData(0);

                giftMessageAction(this._getActionItemId(), params);
            },

            /**
             * Select Gift Receipt and Printed Card
             */
            selectExtraOption: function () {
                var params = this._getGiftWrappingData();
                giftMessageAction(this._getActionItemId(), params);
            },

            /**
             * Retrieve item id for action
             * @returns {string|int}
             */
            _getActionItemId: function () {
                return this.levelIdentifier === 'orderLevel' ? 'order' : this.levelIdentifier;
            },

            /**
             * Retrieve gift message data
             *
             * @param {int|null} id - Current wrapper ID
             * @returns {Object}
             * @private
             */
            _getGiftWrappingData: function (id = null) {
                var params = {
                    extension_attributes: []
                }

                params = this._attachGiftMessageData(params);
                params['extension_attributes'] = this.model.getSubmitParams();

                if (id == 0) {
                    params['extension_attributes']['wrappingId'] = null;
                } else if (id > 0) {
                    params['extension_attributes']['wrappingId'] = id;
                }

                giftWrappingService.addOption(this.levelIdentifier, params['extension_attributes']);

                return params;
            },

            /**
             * Attach gift message data to params
             * @param {Object} params
             * @returns {Object}
             * @private
             */
            _attachGiftMessageData: function(params) {
                var giftMessageParams = giftMessageService.getGiftMessage(this._getActionItemId())

                if (!_.isUndefined(giftMessageParams)) {
                    params = _.extend(
                        params,
                        giftMessageParams
                    );
                }

                return params;
            },

            /**
             * @return {String|*}
             */
            getPrintedCardPriceWithTax: function () {
                return priceUtils.formatPrice(
                    this.getObservable('printedCardPriceInclTax')(),
                    this.model.getPriceFormat()
                );
            },

            /**
             * @return {String|*}
             */
            getPrintedCardPriceWithoutTax: function () {
                return priceUtils.formatPrice(
                    this.getObservable('printedCardPriceExclTax')(),
                    this.model.getPriceFormat()
                );
            },
        });
    }
);
