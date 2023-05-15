define([
    'ko'
], function (ko) {
    'use strict';

    var isVisible = ko.observable(false);

    return {
        isVisible: isVisible
    };
});
