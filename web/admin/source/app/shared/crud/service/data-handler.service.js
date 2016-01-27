(function () {
    'use strict';

    angular
        .module('crud')
        .factory('CRUDDataHandler', CRUDDataHandler);

    CRUDDataHandler.$inject = ['ngDialog', 'SweetAlert', 'RouteHelpers'];

    /**
     * Datatable Data Handler
     *
     * @constructor
     */
    function CRUDDataHandler(ngDialog, SweetAlert, helper) {
        /* jshint validthis: true */
        var dtInstance;

        return {
            datatable: datatable,
            invokeAction: invokeAction
        };

        /**
         * Set or Get Instance of Datatable
         *
         * @param instance
         */
        function datatable(instance) {
            if (instance === undefined) {
                return dtInstance;
            }

            dtInstance = instance;

            return this;
        }

        function invokeAction(data, row) {
            return ({
                create: create,
                editRow: editRow,
                deleteRow: deleteRow
            })[data.callback](data, row);
        }

        function create() {

        }

        function editRow() {

        }

        function deleteRow(data, row) {
           SweetAlert.swal({
                title: data.confirm.title,
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: data.confirm.no.title,
                confirmButtonColor: '#dd6b55',
                confirmButtonText: data.confirm.yes.title
            }, function () {
                console.log(arguments);
            });
        }

    }

})();