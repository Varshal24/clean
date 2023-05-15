define([
    'underscore',
    'mage/utils/wrapper',
    'Aheadworks_OneStepCheckout/js/view/animation'
], function (_, wrapper, animation) {
    'use strict';

    return function (model) {
        return _.extend(model, {

            /**
             * @inheritdoc
             */
            process: wrapper.wrap(
                model.process, function (origAction, response, messageContainer) {
                    origAction(response, messageContainer);
                    if (response.status !== 401 && !messageContainer) {
                        animation.scrollToTop();
                    }
                })
        });
    }
});
