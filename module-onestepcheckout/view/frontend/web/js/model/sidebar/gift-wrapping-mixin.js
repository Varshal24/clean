define(
    [
        'ko',
        'underscore',
        'mage/utils/wrapper',
        'Magento_Customer/js/customer-data'
    ],
    function (ko, _, wrapper, customerData) {
        'use strict';

        return function (proceedToCheckoutFunction) {
            return wrapper.wrap(proceedToCheckoutFunction, function (originalProceedToCheckoutFunction, itemId) {
                var giftWrappingConfig = window.giftOptionsConfig ?
                        window.giftOptionsConfig.giftWrapping :
                        window.checkoutConfig.giftWrapping,
                    model = originalProceedToCheckoutFunction(itemId);

                customerData.get("aw-osc-gift-wrapping-info").subscribe(function (newGiftWrappingConfig) {
                    if (_.size(newGiftWrappingConfig) > 1) {
                        giftWrappingConfig = _.extend(giftWrappingConfig, newGiftWrappingConfig);

                        if (model.itemId == 'orderLevel') {
                            var printedCardPriceInclTax = giftWrappingConfig.cardInfo.hasOwnProperty('price_incl_tax') ?
                                    giftWrappingConfig.cardInfo.price_incl_tax : giftWrappingConfig.cardInfo.price,
                                printedCardPriceExclTax = giftWrappingConfig.cardInfo.hasOwnProperty('price_excl_tax') ?
                                    giftWrappingConfig.cardInfo.price_excl_tax : giftWrappingConfig.cardInfo.price;
                            model.getObservable('wrapping-orderLevel', 'printedCardPriceInclTax')(printedCardPriceInclTax);
                            model.getObservable('wrapping-orderLevel', 'printedCardPriceExclTax')(printedCardPriceExclTax);
                        }
                    }
                }, this);

                return model;
            });
        };
    }
);
