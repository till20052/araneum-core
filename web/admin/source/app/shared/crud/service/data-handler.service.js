(function () {
    'use strict';

    angular
        .module('crud')
        .factory('CRUDDataHandler', CRUDDataHandler);

    CRUDDataHandler.$inject = ['$http', 'ngDialog', 'SweetAlert', 'toaster', 'RouteHelpers', '$filter'];

    /**
     * Datatable Data Handler
     *
     * @constructor
     */
    function CRUDDataHandler($http, ngDialog, SweetAlert, toaster, helper, $filter) {
        /* jshint validthis: true */
        var dtInstance;
        var translate = $filter('translate');

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

        /**
         * Submit form to server
         *
         * @param {object} form
         * @param {{
         *      onSuccess: <function>,
         *      onError: <function>
         * }} triggers
         * @private
         */
        function submit(form, triggers) {
            /* jshint eqeqeq: false */
            $http({
                method: form.method,
                url: form.action,
                data: $.param(form.data),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }).then(
                function (r) {
                    dtInstance.fnDraw();
                    toaster.pop('success', 'Success', r.data.message);
                    if(
                        triggers instanceof Object &&
                        typeof triggers.onSuccess == 'function'
                    ){
                        // @todo необхідно зробити без перезагрузки datatable
                        //dtInstance.fnUpdate(null, $('>tbody>tr', dtInstance));
                        triggers.onSuccess(r);
                    }
                },
                function (r) {
                    toaster.pop('error', 'Error', r.data.message);
                    if(
                        triggers instanceof Object &&
                        typeof triggers.onError == 'function'
                    ){
                        triggers.onError(r);
                    }
                }
            );
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
            openWindow(
                angular.extend(data, {
                    source: data.form
                })
            );
        }

        function update(data, row) {
            openWindow(
                angular.extend(data, {
                    source: data.form + '/' + parseInt($('td:first-child', row).text())
                })
            );
        }

        function remove(data, row) {
            SweetAlert.swal({
                title: translate(data.confirm.title),
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: translate(data.confirm.no.title),
                confirmButtonColor: '#dd6b55',
                confirmButtonText: translate(data.confirm.yes.title)
            }, function (isConfirmed) {
                if (isConfirmed) {
                    $http
                        .post(data.resource, {
                            data: [parseInt($('td:first-child', row).text())]
                        })
                        .success(function () {
                            dtInstance.fnDeleteRow(row);
                        })
                        .error(function () {
                        });
                }
            });
        }

        function setState(data, row) {
            $http
                .post(data.resource, {
                    data: [parseInt($('td:first-child', row).text())]
                })
                .success(function (data) {
                    if (data.hasOwnProperty('success') && data.success === true) {
                        dtInstance.fnUpdate(data.state, row, 3, false);
                    }
                })
                .error(function () {
                });
        }

    }

})();