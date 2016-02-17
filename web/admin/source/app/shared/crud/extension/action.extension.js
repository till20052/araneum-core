(function () {
    'use strict';

    angular
        .module('crud')
        .run(['ActionsFactory', 'Dispatcher', function (Action, Dispatcher) {
            /* jshint validthis: true */

            /**
             * jQuery Extension
             */
            $.fn.extend({
                setAction: create,
                invokeAction: invoke
            });

            /**
             * Set Action
             *
             * @param {Object} options
             * @returns {jQuery}
             */
            function create(options) {
                return $(this)
                    .data('action', new Action(options))
                    .click(function () {
                        Dispatcher.dispatch($(this).data('action'));
                    });
            }

            /**
             * Invoke Action
             */
            function invoke() {
                return $(this).click();
            }

        }]);

})();