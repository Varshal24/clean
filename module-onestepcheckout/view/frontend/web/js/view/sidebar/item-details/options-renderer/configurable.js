define(
    [
        'Aheadworks_OneStepCheckout/js/view/sidebar/item-details/options-renderer/renderer-abstract'
    ],
    function (
        Component
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/sidebar/item-details/options/configurable'
            },

            /**
             * Get swatch config for widget initialization
             *
             * @returns {Object}
             */
            getSwatchConfig: function () {
                return {
                    itemId: this.itemId,
                    jsonConfig: JSON.parse(this.options.jsonConfig),
                    jsonSwatchConfig: JSON.parse(this.options.jsonSwatchConfig),
                    defaultValues: this.options.options.defaultValues
                }
            },
        });
    }
);
