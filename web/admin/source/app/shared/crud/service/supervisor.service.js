(function () {
    'use strict';

    angular
        .module('crud')
        .service('CRUDSupervisor', CRUDSupervisor);

    CRUDSupervisor.$inject = [];

    /**
     *
     * @constructor
     */
    function CRUDSupervisor() {
        /* jshint validthis: true */
        return {
            toolBar: undefined,
            setToolBar: setToolBar,

            dataTable: undefined,
            setDataTable: setDataTable
        };

        function setDataTable(dataTable) {
            this.dataTable = new DataTable(dataTable, this);
            if(this.toolBar instanceof Object)
                this.toolBar.refresh();
        }

        function setToolBar(toolBar) {
            this.toolBar = new ToolBar(toolBar, this);
        }
    }

    function DataTable(dataTable, supervisor) {
        /* jshint validthis: true, eqeqeq: false */
        var dt = dataTable,
            rows = (function(rows){
                return $.map(rows, function (row) {
                    return {
                        element: row,
                        selected: false
                    };
                });
            })($('>tbody>tr', dt.dataTable).toArray());

        return {
            selectRow: selectRow,
            selected: selected
        };

        function selectRow(index, state) {
            if (typeof rows[index] == 'undefined')
                return;
            rows[index].selected = !!state;
            supervisor.toolBar.refresh();
        }

        function selected() {
            return $.map(rows, function (row) {
                if (!row.selected)
                    return null;
                return row;
            });
        }
    }

    function ToolBar(toolBar, supervisor) {
        var buttons = [];

        $('button', toolBar).each(function () {
            buttons.push(this);
        });

        return {
            refresh: refresh
        };

        function refresh() {
            buttons.forEach(function (button) {
                $(button).prop(
                    'disabled',
                    !$(button).data('crud')
                        .available
                        .apply(angular.extend({
                            button: $(button),
                            supervisor: supervisor
                        }, $(button).data('crud')))
                );
            });
        }
    }

})();