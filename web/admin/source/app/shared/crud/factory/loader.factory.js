(function () {
    'use strict';

    angular
        .module('crud')
        .factory('CRUDLoader', CRUDLoader);

    CRUDLoader.$inject = ['$http'];

    /**
     * Interface of CRUD Loader
     *
     * @param $http
     * @returns {Function}
     * @constructor
     */
    function CRUDLoader($http) {
        return function () {
            /* jshint validthis: true,
             eqeqeq: false */

            /** @typedef {String} */
            var url;

            /** @typedef {Object} */
            var promise;

            return {
                setUrl: setUrl,
                getUrl: getUrl,
                load: load,
                clearPromise: clearPromise
            };

            /**
             * Set url value
             *
             * @param {String} value
             * @return {Object}
             */
            function setUrl(value) {
                url = value;

                return this;
            }

            /**
             * Get url value
             *
             * @return {String}
             */
            function getUrl() {
                return url;
            }

            /**
             * Load data by url
             *
             * @param {{
             *  onSuccess: <Function>
             *  onError: <Function>
             * }} customTriggers
             * @return {Object}
             */
            function load(customTriggers) {
                var triggers = {
                    onSuccess: function () {
                    },
                    onError: function () {
                    }
                };

                if (customTriggers instanceof Object) {
                    Object.keys(triggers).forEach(function (key) {
                        if (customTriggers.hasOwnProperty(key)) {
                            triggers[key] = customTriggers[key];
                        }
                    });
                }

                if (typeof promise == 'undefined') {
                    promise = $http({
                        url: getUrl(),
                        method: 'GET'
                    });
                }

                promise.then(
                    function (r) {
                        clearPromise();
                        triggers.onSuccess(r.data, r.status, r);
                    },
                    function (r) {
                        clearPromise();
                        triggers.onError(r.data, r.status, r);
                    }
                );

                return this;
            }

            /**
             * Clear loader promise
             */
            function clearPromise() {
                promise = undefined;
            }
        };
    }

})();