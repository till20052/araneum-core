(function () {
    'use strict';

    angular
        .module('crud')
        .service('supervisor', supervisor);

    supervisor.$inject = ['CRUDLoader'];

    /**
     * CRUD Supervisor
     *
     * @param CRUDLoader
     * @returns {{
     *      loader: {
     *          config: CRUDLoader,
     *          form: CRUDLoader
     *      },
     *      toolBar: <Function>
     * }}
     */
    function supervisor(CRUDLoader) {
        /* jshint validthis: true */
        var register = {};

        return {
            loader: {
                config: new CRUDLoader(),
                form: new CRUDLoader()
            },
            toolBar: toolBar
        };

        function toolBar(id, container) {
            if (!register.hasOwnProperty('toolBar'))
                register.toolBar = {};

            if (container !== undefined)
                register.toolBar[id] = container;

            return register.toolBar.hasOwnProperty(id) ?
                register.toolBar[id] :
                $('div#' + id);
        }

        /**
         *
         */
        function fnDataTable(data) {

        }

        function setDataTable(dataTable) {
            this.dataTable = new DataTable(dataTable, this);
            //if(this.toolBar instanceof Object)
            //    this.toolBar.refresh();
        }
    }

    function DataTable(dataTable, supervisor) {
        /* jshint validthis: true, eqeqeq: false */
        var dt = dataTable,
            rows = (function (rows) {
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


})();