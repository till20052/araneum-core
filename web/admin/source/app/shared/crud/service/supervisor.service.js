(function () {
    /* jshint validthis: true */
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
        var register = {},
            _register = new Register();

        return {
            $: new Register(),
            loader: {
                config: new CRUDLoader(),
                form: new CRUDLoader()
            },
            _loader: loader,
            toolBar: toolBar,
            dataTable: dataTable
        };

        function loader(id) {
            if(_register.get('loader') === undefined)
                _register.set('loader', new Register());


            _register
                .get('loader')
                .set(id, new CRUDLoader());

            console.log(this.$.get('loader') === undefined);

            this.$.set('loader', new Register());

            console.log(this.$.get('loader'));
        }

        /**
         * Set|Get jQuery toolBar element in|from register
         *
         * @param {Number|String} id
         * @param {jQuery} element
         * @returns {jQuery}
         */
        function toolBar(id, element) {
            if (!register.hasOwnProperty('toolBar'))
                register.toolBar = {};

            if (element !== undefined)
                register.toolBar[id] = element;

            return register.toolBar.hasOwnProperty(id) ?
                register.toolBar[id] :
                $('div#' + id);
        }

        /**
         * Set|Get jQuery dataTable element in|from register
         *
         * @param {Number|String} id
         * @param {jQuery} element
         * @returns {jQuery}
         */
        function dataTable(id, element) {
            if (!register.hasOwnProperty('dataTable'))
                register.dataTable = {};

            if (element !== undefined)
                register.dataTable[id] = element;

            return register.dataTable.hasOwnProperty(id) ?
                register.dataTable[id] :
                $('div#' + id);
        }
    }

    /**
     * CRUD Supervisor Register
     *
     * @returns {{
     *      set: set,
     *      get: get
     * }}
     * @constructor
     */
    function Register() {
        var $ = {},
            srv = {
                set: set,
                get: get
            };

        return srv;

        /**
         * Set value in to container of register
         *
         * @param {Number|String} id
         * @param {*} value
         * @returns {srv}
         */
        function set(id, value) {
            $[id] = value;

            return this;
        }

        /**
         * Get value from container of register
         *
         * @param {Number|String} id
         * @returns {*}
         */
        function get(id) {
            return $[id];
        }
    }


})();