(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDDataTableController', CRUDDataTableController);

    CRUDDataTableController.$inject = ['$scope', '$state', '$compile', 'CRUDSupervisor'];

    /**
     * CRUD DataTable Controller
     *
     * @constructor
     */
    function CRUDDataTableController($scope, $state, $compile, supervisor) {
        /* jshint validthis: true */
        var dt = this,
            controls = [
                '<crud-dropdown />',
                '<crud-checkbox />'
            ];

        dt.instance = {};
        dt.options = {
            processing: true,
            serverSide: true,
            sPaginationType: 'full_numbers',
            fnServerData: getServerData
        };

        /**
         * Get data for datatable from server
         *
         * @param source
         * @param data
         * @param callback
         * @param settings
         */
        function getServerData(source, data, callback, settings) {
            settings.jqXHR = $.ajax({
                dataType: 'json',
                type: 'POST',
                url: source,
                data: data,
                success: function (r) {
                    callback(prepareData(r));
                    compileControls($('>tbody>tr>td>*', dt.instance.dataTable).toArray());
                    supervisor.setDataTable(dt.instance);
                },
                error: function (r) {
                    if (r.status === 401) {
                        $state.go('login');
                    }
                }
            });
        }

        function prepareData(data) {
            return angular.extend(data, {
                aaData: $.map(data.aaData, function (cols) {
                    return [cols.splice(0, cols.length - 1).concat(controls)];
                })
            });
        }

        function compileControls(controls) {
            controls.forEach(function (control) {
                if ($(control).prop('tagName').toString().match(/^crud\-.*$/ig))
                    $compile(control)($scope);
            });
        }
    }

})();