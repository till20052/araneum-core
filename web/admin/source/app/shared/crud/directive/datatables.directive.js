(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudDatatable', CRUDDatatableDirective);

    CRUDDatatableDirective.$inject = ['$compile', 'RouteHelpers'];

    /**
     * CRUD Datatable directive constructor
     *
     * @param $compile
     * @param helper
     * @returns {Object}
     * @constructor
     */
    function CRUDDatatableDirective($compile, helper) {
        return {
            link: link,
            controller: DatatableController,
            restrict: 'E',
            controllerAs: 'vm',
            templateUrl: helper.basepath('crud/datatable.html'),
            replace: true
        };

        function link(scope, iElement, iAttrs, controller) {
            scope.$watch(function(){
                return $('table', iElement).length > 0;
            }, function(ready){
                if( ! ready){
                    return;
                }
                //$('table', iElement).click(function(event){
                //    controller.onDatatableClick(event, this);
                //});
            });

            scope.$watch(function () {
                return $('table div.checkbox', iElement).length;
            }, function (count) {
                if (count > 0) {
                    $('table div.checkbox', iElement)
                        .addClass('mr0')
                        .parent()
                        .addClass('text-center')
                        .find('span.fa-check')
                        .addClass('mr0');
                }
            });

            scope.$watch(function () {
                return $('table>tbody crud-ui', iElement).length;
            }, function (count) {
                if (count > 0) {
                    $compile($('table>tbody', iElement))(scope);
                }
            });


        }
    }

    DatatableController.$inject = ['CRUDConfigLoader', '$state'];

    /**
     * CRUD Datatable Controller
     *
     * @param CRUDConfigLoader
     * @param $state
     * @constructor
     */
    function DatatableController(CRUDConfigLoader, $state) {
        /* jshint validthis: true */
        var vm = this;

        vm.isInitialized = false;

        vm.datatable = {
            instance: {},
            options: {
                processing: true,
                serverSide: true,
                sPaginationType: 'full_numbers',
                fnServerData: getServerData
            },
            columns: []
        };

        vm.onDatatableClick = onDatatableClick;

        activate();

        /**
         * Activation
         */
        function activate() {
            CRUDConfigLoader.load({
                onSuccess: function (data) {
                    vm.isInitialized = true;
                    vm.datatable.columns = data.grid.columns;
                    vm.datatable.options.sAjaxSource = data.grid.source;
                }
            });
        }

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
                    onGetServerDataSuccess(r, callback);
                },
                error: function (r) {
                    if (r.status === 401) {
                        $state.go('login');
                    }
                }
            });
        }

        /**
         * Response transformer. Invokes in case if data,
         * which getting from server, received successfully
         *
         * @param {{
         *  aaData: Array,
         *  iTotalDisplayRecords: Number,
         *  iTotalRecords: Number
         * }} response
         * @param {Function} callback
         */
        function onGetServerDataSuccess(response, callback) {
            response.aaData.forEach(function (row, i) {
                this[i] = row
                    .splice(0, row.length - 1)
                    .concat([
                        '<crud-ui type="actions" view="menu" />',
                        '<crud-ui type="checkbox" />'
                    ]);
            }, response.aaData);
            callback(response);
        }

        /**
         * Event handler on click.
         */
        function onDatatableClick(event, table) {
            console.log(event.target, table);
        }
    }

})();