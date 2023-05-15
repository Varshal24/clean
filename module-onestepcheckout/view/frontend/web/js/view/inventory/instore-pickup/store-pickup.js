define(
    [
        'Magento_InventoryInStorePickupFrontend/js/view/store-pickup',
        'Magento_Checkout/js/model/quote',
        'Aheadworks_OneStepCheckout/js/model/inventory/instore-pickup/state'
    ],
    function (
        StorePickupComponent,
        quote,
        instorePickupState
    ) {
        'use strict';

        return StorePickupComponent.extend({

            /**
             * @inheritdoc
             */
            initialize: function () {
                this._super();

                instorePickupState.isStorePickupSelected(
                    this.isStorePickupSelected()
                );

                this.isStorePickupSelected.subscribe(
                    function (isStorePickupSelected) {
                        instorePickupState.isStorePickupSelected(
                            isStorePickupSelected
                        );
                    },
                    this
                );
            },

            /**
             * @inheritdoc
             */
            syncWithShipping: function () {
                quote.isQuoteVirtual.subscribe(
                    function (isQuoteVirtual) {
                        this.updateVisibility(isQuoteVirtual);
                    },
                    this
                );
                this.updateVisibility(quote.isQuoteVirtual());
            },

            /**
             * Update component visibility
             *
             * @param {Boolean} isQuoteVirtual
             * @returns {Boolean|*}
             */
            updateVisibility: function (isQuoteVirtual) {
                return this.isVisible(
                    this.isAvailable
                    && !isQuoteVirtual
                );
            }
        });
    }
);
