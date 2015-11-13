(function (angular) {
    'use strict';

    angular
        .module('app.listener')
        .factory('HTTPEventInterceptor', HTTPEventInterceptor);

    HTTPEventInterceptor.$inject = ['$q', 'HTTPEventListenerService'];
    function HTTPEventInterceptor($q, HTTPEventListenerService) {
        return {
            request: onRequest,
            response: onResponse,
            error: onError
        };

        function onRequest(request){
            HTTPEventListenerService
	            .triggerEvents('onRequest', request);

            return request;
        }

        function onResponse(response){
            HTTPEventListenerService
	            .triggerEvents('onResponse', response);

            return response;
        }

        function onError(response){
	        HTTPEventListenerService
		        .triggerEvents('onError', response);

            return $q.reject(response);
        }
    }

})(angular);