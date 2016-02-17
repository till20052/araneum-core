(function () {
    'use strict';

    angular
        .module('crud')
        .factory('ActionsFactory', ActionsFactory);

    ActionsFactory.$inject = [];

    /**
     * @constructor
     */
    function ActionsFactory() {
        return Action;
    }

    /**
     * @constructor
     */
    function Action(options) {
        var map = {
            create: 'create',
            update: 'update',
            editRow: 'setState',
            deleteRow: 'remove'
        };

        if (!map.hasOwnProperty(options.callback))
            throw console.error('[ERROR]: Event "' + options.callback + '" doesn\'t defined');

        return ({
            create: create,
            update: update,
            setState: setState,
            remove: remove,
            submit: submit
        })[map[options.callback]];

        /**
         * Create
         */
        function create() {
            return options.form;
        }

        /**
         * Update
         */
        function update() {

        }

        /**
         *
         */
        function submit() {

        }

        /**
         * Set State
         */
        function setState() {

        }

        /**
         * Remove
         */
        function remove() {

        }
    }

})();