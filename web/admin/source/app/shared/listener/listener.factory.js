(function (angular) {
    'use strict';

    angular
        .module('app.listener')
        .factory('HTTPEventInterceptor', HTTPEventInterceptor);

    HTTPEventInterceptor.$inject = ['$q', '$rootScope', 'HTTPEventListenerService'];
    function HTTPEventInterceptor($q, $rootScope, HTTPEventListenerService) {
        return {
            request: onRequest,
            response: onResponse,
            responseError: onError
        };

        function httpEvent(content) {
            return {
                content: content,
                state: $rootScope.$state
            };
        }

        function onRequest(request) {
            HTTPEventListenerService
                .triggerEvents('onRequest', httpEvent(request));

            return request;
        }

        function onResponse(response) {
            HTTPEventListenerService
                .triggerEvents('onResponse', httpEvent(response));

            return response;
        }

        function onError(response) {
            HTTPEventListenerService
                .triggerEvents('onError', httpEvent(response));

            return $q.reject(response);
        }
    }

})(angular);