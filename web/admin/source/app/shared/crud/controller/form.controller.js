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
            children: {}
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