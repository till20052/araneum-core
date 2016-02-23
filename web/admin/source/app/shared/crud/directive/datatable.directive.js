(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudDatatable', CRUDDataTableDirective);

    CRUDDataTableDirective.$inject = ['$compile', 'supervisor'];

    /**
     * CRUD DataTable Directive
     *
     * @param $compile
     * @param supervisor
     * @returns {Object}
     * @constructor
     */
    function CRUDDataTableDirective($compile, supervisor) {
        var controller;

        //$.fn = angular.extend($.fn, {
        //    getSelectedRows: getSelectedRows,
        //    selectRow: selectRow
        //});

        return {
            link: link,
            restrict: 'E',
            controller: 'CRUDDataTableController',
            controllerAs: 'controller',
            scope: {
                datatable: '=manifest',
                toolBarId: '@toolbar'
            }
        };

        /**
         * Directive link
         *
         * @param scope
         * @param element
         */
        function link(scope, element) {
            if (!(scope.datatable instanceof Object))
                return;

            controller = scope.controller;
            scope.datatable = manifest(scope.datatable);

            var stopWatch = scope.$watch(function () {
                return controller.options.hasOwnProperty('sAjaxSource') && controller.options.sAjaxSource.length > 0;
            }, function (ready) {
                if (ready) {
                    element.replaceWith($compile(createTable({
                        columns: scope.datatable.columns
                    }))(scope));
                    stopWatch();
                }
            });
        }

        function manifest(datatable) {
            /* jshint validthis: true */
            return angular.extend(datatable, {
                setColumns: setColumns,
                setSource: setSource
            });

            function setColumns(columns) {
                this.columns = columns;

                return this;
            }

            function setSource(source) {
                controller.options.sAjaxSource = source;

                return this;
            }
        }

        /**
         * Create table
         *
         * @param data
         * @returns {JQuery|jQuery}
         */
        function createTable(data) {
            return $('<table class="table-bordered table-striped hover" />')
                .attr({
                    datatable: 'crud',
                    'dt-instance': 'controller.instance',
                    'dt-options': 'controller.options'
                })
                .append(createTableHead(data.columns));
        }

        /**
         * Create head of table
         *
         * @param columns
         * @returns {JQuery|jQuery}
         */
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

        /**
         * Get selected rows
         *
         * @returns {Array<JQuery|jQuery>}
         */
        function getSelectedRows() {
            /* jshint validthis: true */
            if (['TABLE', 'TBODY'].indexOf(this.prop('tagName')) < 0)
                return [];
            return $('> tr, > tbody > tr', this)
                .filter(function () {
                    return $(this).data('selected') === true;
                })
                .toArray();
        }

        /**
         * Set row state selection
         *
         * @param state
         */
        function selectRow(state) {
            /* jshint validthis: true, eqeqeq: false */
            if (this.prop('tagName') != 'TR')
                return;
            this.data('selected', !!state);
        }
    }

})();