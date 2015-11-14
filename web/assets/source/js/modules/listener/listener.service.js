(function (angular) {
    'use strict';

    angular
        .module('app.listener')
        .service('HTTPEventListenerService', HTTPEventListenerService);

    HTTPEventListenerService.$inject = ['HTTPEventListener'];
    function HTTPEventListenerService(HTTPEventListener) {
        return {
            triggerEvents: triggerEvents
        };

        function triggerEvents(onCase, response) {
            HTTPEventListener
                .getEventsByCase(onCase)
                .forEach(function (callback) {
                    callback(response);
                });
        }
    }

})(angular);