(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDDataTableController', CRUDDataTableController);

    CRUDDataTableController.$inject = ['$scope', '$state', '$compile', 'supervisor'];

    /**
     * CRUD DataTable Controller
     *
     * @constructor
     */
    function CRUDDataTableController($scope, $state, $compile, supervisor) {
        /* jshint validthis: true, eqeqeq: false */
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
                success: function (data) {
                    callback(angular.extend(data, {
                        aaData: $.map(data.aaData, function (cols) {
                            return [cols.splice(0, cols.length - 1).concat(controls)];
                        })
                    }));
                    $compile($('> tbody > tr', dt.instance.dataTable)
                        .data({
                            selected: false
                        })
                        .find('> td > *')
                        .filter(function () {
                            return /^crud\-.*$/ig.test($(this).prop('tagName'));
                        })
                    )($scope);
                },
                error: function (r) {
                    if (r.status === 401) {
                        $state.go('login');
                    }
                }
            });
        }
    }

})();