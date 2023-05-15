define(
    [],
    function (Component) {
        'use strict';

        /**
         * Get street
         *
         * @param {string|object|array} street
         * @return string
         */
        return function(street) {
            var preparedStreet = typeof street == "string"
                ? street
                : '';

            if (typeof street == "object") {
                var dataStreet = [];
                for (const [key, value] of Object.entries(street)) {
                    dataStreet.push(value)
                }
                preparedStreet = dataStreet.join(' ')
            }

            return preparedStreet;
        }
    }
);
