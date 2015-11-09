(function () {
    'use strict';

    angular
        .module('app.loading')
        .factory('LoadingInterceptor', LoadingInterceptor)

    LoadingInterceptor.$inject = ['$q']
    function LoadingInterceptor($q) {
        var pendingRequests = [];

        function onRequest(request) {
            console.log('Pending request: ' + request.url);

            return request;
        }

        function onResponse(response) {
            console.log('Completed request: ' + response.config.url);

            return response;
        }

        function onError(response) {
            console.log('Failed request: ' + response.config.url);

            return $q.reject(response);
        }

        return {
            request: onRequest,
            response: onResponse,
            requestError: onError,
            responseError: onError
        };
    }

})();