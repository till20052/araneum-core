(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDFormController', CRUDFormController);

    CRUDFormController.$inject = [];

    /**
     * CRUD Form Controller
     *
     * @constructor
     */
    function CRUDFormController() {
        /* jshint validthis: true */
        var vm = this;

        vm.form = {
            data: {},
            children: {},
            // @todo need to move this object to separated service
            dispatcher: (function () {
                var events = {
                    click: click,
                    submit: submit
                };

                return {
                    dispatch: dispatch
                };

                /**
                 * Dispatch Event
                 *
                 * @param event
                 * @param object
                 * @returns {*}
                 */
                function dispatch(event, object) {
                    if(!events.hasOwnProperty(event))
                        event = 'click';
                    return events[event](object);
                }
            })()
        };

        vm.linkKeys = linkKeys;

        /**
         * Link keys
         *
         * @param {Array} tokens
         * @returns {{from: String, to: String}}
         */
        function linkKeys(tokens) {
            /* jshint eqeqeq: false */
            return {from: tokens[0], to: typeof tokens[1] != 'undefined' ? tokens[1] : tokens[0]};
        }
    }

})();