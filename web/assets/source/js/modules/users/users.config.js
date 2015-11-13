(function(angular){
    'use strict';

    angular
        .module('app.users')
        .config(['$httpProvider', 'HTTPEventListenerProvider', function($httpProvider, HTTPEventListenerProvider){
            $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

	        HTTPEventListenerProvider.onRequest(function(response){
		        //console.log(response);
	        });
        }]);

})(angular);