(function () {
    /* jshint validthis: true */
    'use strict';

    angular
        .module('crud')
        .service('supervisor', Supervisor);

    Supervisor.$inject = ['CRUDLoader'];

    /**
     *
     * @param CRUDLoader
     * @returns {{
     *      loader: loader,
     *      eventsFactory: EventsFactory,
     *      dispatcher: Dispatcher
     * }}
     * @constructor
     */
    function Supervisor(CRUDLoader) {
        var register = new Register();

        return {
            loader: loader,
            eventsFactory: new EventsFactory(),
            dispatcher: new Dispatcher()
        };

        /**
         * Set|Get sub-register in|from register
         *
         * @param id
         * @returns {*}
         */
        function subRegister(id) {
            if (register.get(id) === undefined)
                register.set(id, new Register());

            return register.get(id);
        }

        /**
         * Set|Get loader by id
         *
         * @param {String} id
         * @returns {CRUDLoader}
         */
        function loader(id) {
            if (subRegister('loader').get(id) === undefined)
                subRegister('loader').set(id, new CRUDLoader());

            return subRegister('loader').get(id);
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
         * Set value into container of register
         *
         * @param id
         * @param value
         * @returns {{
         *      set: set,
         *      get: get
         * }}
         */
        function set(id, value) {
            $[id] = value;
            return srv;
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

    /**
     *
     * @returns {{
     *      createEvent: createEvent
     * }}
     * @constructor
     */
    function EventsFactory() {
        var mapping = {
            create: 'create',
            update: 'update',
            editRow: 'setState',
            deleteRow: 'remove'
        };

        return {
            createEvent: createEvent
        };

        /**
         * Create an Event
         *
         * @param data
         */
        function createEvent(data) {
            if (!mapping.hasOwnProperty(data.callback))
                throw console.error('[ERROR]: Event "' + data.callback + '" doesn\'t defined');

            return {
                event: mapping[data.callback]
            };
        }
    }

    /**
     *
     * @constructor
     */
    function EventListener() {

        return {};

    }

    /**
     *
     * @returns {{
     *      dispatch: dispatch
     * }}
     * @constructor
     */
    function Dispatcher() {

        return {
            dispatch: dispatch
        };

        function dispatch(event) {
            console.log(event);
        }

    }

})();