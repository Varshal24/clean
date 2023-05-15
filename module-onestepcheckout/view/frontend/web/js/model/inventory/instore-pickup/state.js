define(
    [
        'ko'
    ],
    function (
        ko
    ) {
        'use strict';

        var isStorePickupSelectedFlag = ko.observable(false),
            isPickupLocationSelectedFlag = ko.observable(false)
        ;

        return {
            isStorePickupSelected: isStorePickupSelectedFlag,
            isPickupLocationSelected: isPickupLocationSelectedFlag
        };
    }
);
