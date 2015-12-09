(function(angular){
    'use strict';

    angular
        .module('app.listener')
        .config(['$httpProvider', function($httpProvider){
            $httpProvider.interceptors.push('HTTPEventInterceptor');
        }]);

})(angular);