(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDActionsController', CRUDActionsController);

    CRUDActionsController.$inject = ['$scope', 'ngDialog'];

    function CRUDActionsController($scope, ngDialog) {
        var vm = this,
            actions = {
                create: create,
                update: update,
                deleteRow: remove,
                editRow: setState
            };

        vm.click = click;

        /**
         * Open new ngDialod with editable form
         *
         * @param data
         * @private
         */
        function openWindow(data) {
            ngDialog.open({
                template: 'crud/dialog.html',
                controller: 'CRUDDialogController',
                controllerAs: 'ngDialog',
                data: data
            });
        }

        function click($event) {
            var config = $($event.currentTarget).data('config');

            invokeAction(config.callback, config);
        }

        function invokeAction(name, options) {
            if (!actions.hasOwnProperty(name)) {
                return console.error('[ERROR]: Try to invoke action: ' + name + ', but this action doesn\'t defined');
            }

            return actions[name](options);
        }

        /**
         *
         * @param options
         */
        function create(options) {
            openWindow({
                icon: options.display.icon,
                title: options.display.label,
                form: {
                    source: options.form
                }
            });
        }

        // update
        function update() {
            console.log(123);
        }

        // set state
        function setState() {

        }

        // delete
        function remove() {

        }
    }

})();