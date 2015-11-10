(function(angular){
    'use strict';

    angular
        .module('app.listener')
        .provider('HTTPEventListener', HTTPEventListener);

    HTTPEventListener.$inject = [];
    function HTTPEventListener(){
        var events = {
            onResponse: []
        };

        return {
            $get: function(){
                return {
                    onResponse: onResponse,
                    getEventsByCase: getEventsByCase
                };
            }
        };

        function registerEvent(onCase, event) {
            if (typeof event != 'function') {
                return false;
            }

            events[onCase].push(event);

            return true;
        }

        function onResponse(event) {
            registerEvent('onResponse', event);
        }

        function getEventsByCase(onCase){
            return events[onCase];
        }
    }
})(angular);
