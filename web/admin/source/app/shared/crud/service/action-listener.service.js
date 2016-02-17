(function () {
    'use strict';

    angular
        .module('crud')
        .service('ActionListener', ActionListener);

    ActionListener.$inject = ['ngDialog'];

    /**
     * CRUD Action Listener
     *
     * @constructor
     */
    function ActionListener(ngDialog) {
        return {
            create: create,
            setState: setState
        };

        function openWindow(data) {
            ngDialog.open({
                template: 'crud/dialog.html',
                controller: 'CRUDDialogController',
                controllerAs: 'ngDialog',
                data: data
            });
        }

        /**
         * Create form
         */
        function create(event) {
            openWindow({
                title: 'test',
                form: {
                    source: event
                }
            });
        }
    }

})();