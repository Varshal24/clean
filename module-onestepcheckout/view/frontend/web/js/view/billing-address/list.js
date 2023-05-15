define([
    'ko',
    'Magento_Customer/js/model/address-list'
], function (ko, addressList) {
    'use strict';

    return ko.observableArray(addressList().slice());
});
