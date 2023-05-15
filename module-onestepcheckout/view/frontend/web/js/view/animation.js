define([
    'jquery'
], function ($) {
    'use strict';

    var animationMs = 300;

    return {

        /**
         * Scroll to element in case it is out of view
         *
         * @param {Object} element
         */
        scrollToElement: function (element) {
            var elementOffset = element.offset().top,
                visibleAreaStart = $(window).scrollTop(),
                visibleAreaEnd = visibleAreaStart + window.innerHeight;

            if (elementOffset < visibleAreaStart || elementOffset > visibleAreaEnd) {
                $('html,body').animate(
                    {
                        scrollTop: elementOffset - window.innerHeight / 3
                    },
                    animationMs
                );
            }
        },

        /**
         * Scroll top top
         */
        scrollToTop: function () {
            $('html,body').animate(
                {
                    scrollTop: 0
                },
                animationMs
            );
        }
    }
});
