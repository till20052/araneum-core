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
            scope.$watch(function () {
                return $('table', iElement).length > 0;
            }, function (ready) {
                if (!ready) {
                    return;
                }
                $('table', iElement).click(function (event) {
                    controller.onDatatableClick(event, this);
                });
            });

            var observer = new MutationObserver(function () {
                var table = $('table', iElement);
                if (table.length > 0) {
                    $compile($('>tbody', table))(scope);
                    $('div.checkbox', table)
                        .addClass('mr0')
                        .parent()
                        .addClass('text-center')
                        .find('span.fa-check')
                        .addClass('mr0');
                }
            });

            observer.observe(iElement[0], {
                childList: true,
                subtree: true
            });
        }
    }

    DatatableController.$inject = ['CRUDConfigLoader', 'CRUDDataHandler', '$state'];

    /**
     * CRUD Datatable Controller
     *
     * @param CRUDConfigLoader
     * @param CRUDDataHandler
     * @param $state
     * @constructor
     */
    function DatatableController(CRUDConfigLoader, CRUDDataHandler, $state) {
        /* jshint validthis: true */
        var vm = this;

        vm.isInitialized = false;

        /**
         * @type {{
         *      instance: {
         *          dataTable: object
         *      },
         *      options: {
         *          processing: boolean,
         *          serverSide: boolean,
         *          sPaginationType: string,
         *          fnServerData: getServerData
         *      },
         *      columns: Array
         * }}
         */
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
        function onDatatableClick(event) {
            /* jshint eqeqeq: false */
            var ui = $(event.target);

            if (['A', 'INPUT'].indexOf(ui.prop('tagName')) === -1) {
                return;
            }

            if (
                ui.prop('tagName') == 'A' &&
                ui.data('crud-action') !== undefined
            ) {
                CRUDDataHandler
                    .datatable(vm.datatable.instance.dataTable)
                    .invokeAction(ui.data('crud-action'), ui.parents('tr').eq(0));
            }

            //CRUDDataHandler.getAction(cond);

            //vm.datatable.instance.dataTable.fnUpdate('<span style="color: red">Changed...</span>', $(event.target).parents('tr').eq(0), 1, false);
        }
    }

})();