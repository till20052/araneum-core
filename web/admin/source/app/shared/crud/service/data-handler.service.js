(function () {
    'use strict';

    angular
        .module('crud')
        .factory('CRUDDataHandler', CRUDDataHandler);

    CRUDDataHandler.$inject = ['$http', 'ngDialog', 'SweetAlert', 'RouteHelpers'];

    /**
     * Datatable Data Handler
     *
     * @constructor
     */
    function CRUDDataHandler($http, ngDialog, SweetAlert, helper) {
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
                update: update,
                deleteRow: remove,
                editRow: setState
            })[data.callback](data, row);
        }

        function create(data) {
            ngDialog.open({
                template: helper.basepath('crud/form.html')
            });
        }

        function update(data) {
            ngDialog.open({
                template: helper.basepath('crud/dialog.html'),
                controller: 'CRUDDialogController',
                controllerAs: 'vm',
                data: {
                    icon: data.display.icon,
                    title: data.display.label,
                    form: data.form
                }
            });
        }

        function remove(data, row) {
            SweetAlert.swal({
                title: data.confirm.title,
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: data.confirm.no.title,
                confirmButtonColor: '#dd6b55',
                confirmButtonText: data.confirm.yes.title
            }, function () {
                $http
                    .post(data.resource, {
                        data: [parseInt($('td:first-child', row).text())]
                    })
                    .success(function(){
                        dtInstance.fnDeleteRow(row);
                    });
            });
        }

        function setState(data, row) {
            $http
                .post(data.resource, {
                    data: [parseInt($('td:first-child', row).text())]
                })
                .success(function(data){
                    if(data.hasOwnProperty('success') && data.success === true){
                        dtInstance.fnUpdate(data.state, row, 3, false);
                    }
                });
        }

    }

})();