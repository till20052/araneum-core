(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudDatatable', CRUDDataTableDirective);

    CRUDDataTableDirective.$inject = ['CRUDConfigLoader', '$compile'];

    /**
     * CRUD DataTable Directive
     *
     * @param CRUDConfigLoader
     * @param $compile
     * @returns {Object}
     * @constructor
     */
    function CRUDDataTableDirective(CRUDConfigLoader, $compile) {
        var controller;

        return {
            link: link,
            restrict: 'E',
            controller: 'CRUDDataTableController',
            controllerAs: 'controller'
        };

        function link(scope, element) {
            controller = scope.controller;
            CRUDConfigLoader.load({
                onSuccess: function (data) {
                    controller.options.sAjaxSource = data.grid.source;
                    element.replaceWith($compile(createTable({
                        columns: data.grid.columns
                    }))(scope));
                }
            });
        }

        function createTable(data) {
            return $('<table class="table-bordered table-striped hover" />')
                .attr({
                    datatable: 'crud',
                    'dt-instance': 'controller.instance',
                    'dt-options': 'controller.options'
                })
                .append(createTableHead(data.columns));
        }

        function createTableHead(columns) {
            return $('<thead />').append(
                $('<tr />').append(
                    $.map(columns, function (column) {
                        return $('<th class="bt0 bl0" />')
                            .html('{{ "' + column + '" | translate }}');
                    }),
                    $('<th class="bt0 bl0" />')
                        .attr({
                            width: 1,
                            'data-sortable': false
                        }),
                    $('<th class="bt0 bl0 p text-center" />')
                        .attr({
                            width: 1,
                            'data-sortable': false
                        })
                        .append($('<crud-checkbox />'))
                )
            );
        }
    }

})();