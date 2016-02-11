(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDActionsController', CRUDActionsController);

    CRUDActionsController.$inject = ['ngDialog'];

    function CRUDActionsController(ngDialog) {
        var vm = this,
            actions = {
                create: create
            };

        vm.defineAction = defineAction;

        function defineAction(event) {
            var config = $(event.currentTarget).data('config');
            invokeAction(config.callback, config);
        }

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
    }

})();