define(
    [
        'underscore',
        'mage/utils/wrapper'
        ,
        'Aheadworks_OneStepCheckout/js/model/inventory/instore-pickup/state'
    ],
    function (
        _,
        wrapper,
        instorePickupState
    ) {
        'use strict';

        return function (model) {
            return _.extend(model, {
                /**
                 * @inheritdoc
                 */
                selectForShipping: wrapper.wrap(
                    model.selectForShipping,
                    function (origAction, location) {
                        origAction(location);

                        instorePickupState.isPickupLocationSelected(
                            !(
                                _.isEmpty(
                                    this.selectedLocation()
                                )
                            )
                        );
                    })
            });
        }
    }
);
