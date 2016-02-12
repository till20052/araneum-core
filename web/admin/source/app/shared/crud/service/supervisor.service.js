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
            toolBar: toolBar,
            dataTable: dataTable
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

        function dataTable(id, container) {
            if (!register.hasOwnProperty('dataTable'))
                register.dataTable = {};

            if (container !== undefined)
                register.dataTable[id] = container;

            return register.dataTable.hasOwnProperty(id) ?
                register.dataTable[id] :
                $('div#' + id);
        }
    }


})();