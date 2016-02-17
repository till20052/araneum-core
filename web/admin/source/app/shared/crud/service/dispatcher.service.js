(function(){
    'use strict';

    angular
        .module('crud')
        .service('Dispatcher', Dispatcher);

    Dispatcher.$inject = ['ActionListener'];

    /**
     *
     * @constructor
     */
    function Dispatcher(ActionListener) {
        return {
            dispatch: dispatch
        };

        /**
         *
         */
        function dispatch(event) {
            ActionListener[event.name](event());
        }
    }

})();