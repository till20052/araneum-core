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
     * Directive link
     *
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
            remove: remove
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
         * Set State
         */
        function setState() {

        }

        /**
         * Remove
         */
        function remove() {}
    }

})();