define(
    [
        'jquery'
    ],
    function ($) {
        'use strict';

        var checkoutConfig = window.checkoutConfig,
            isEnabledDefaultPlaceOrderButton = checkoutConfig
                ? checkoutConfig.isEnabledDefaultPlaceOrderButton
                : false;

        return function (renderer) {
            return renderer.extend({

                onActiveChange: function () {
                    var self = this,
                        intervalId,
                        placeOrderSelector = '.aw-onestep-sidebar-content .actions-toolbar .action.checkout',
                        newPlaceOrderId = 'aw-osc-nmi-pay-button';

                    if (isEnabledDefaultPlaceOrderButton) {
                        return this._super();
                    }

                    this.paymentSelector = '#' + newPlaceOrderId;
                    if (!this.active() || !this.renderer) {
                        $(placeOrderSelector).removeAttr('id');
                        return;
                    }

                    intervalId = setInterval(function () {
                        if ($(placeOrderSelector).length) {
                            clearInterval(intervalId);
                            setTimeout(function () {
                                $(placeOrderSelector).attr('id', newPlaceOrderId);
                                self.initNmi();
                            }, 1000);
                        }
                    }, 500);
                },
            });
        }
    }
);
