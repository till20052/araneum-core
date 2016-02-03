(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDFormController', CRUDFormController);

    CRUDFormController.$inject = [];

    /**
     * CRUD Form Controller
     * @constructor
     */
    function CRUDFormController() {
        /* jshint validthis: true */
        var vm = this;

        vm.form = {
            data: {},
            children: {}
        };

        vm.submit = submit;
        vm.click = click;

        activate();

        /**
         * Activation
         * @private
         */
        function activate() {

        }

        function submit() {

        }

        function click(event) {
            console.log(event);
        }

    }

})();