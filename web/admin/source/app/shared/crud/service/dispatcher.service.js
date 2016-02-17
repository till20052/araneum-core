(function(){
    'use strict';

    angular
        .module('crud')
        .service('Dispatcher', Dispatcher);

    Dispatcher.$inject = ['ActionListener'];

    /**
     * CRUD Dispatcher
     *
     * @constructor
     */
    function Dispatcher(ActionListener) {
        return {
            dispatch: dispatch
        };

        /**
         * Dispatch event
         */
        function dispatch(event) {
            ActionListener[event.name](event());
        }
    }

})();