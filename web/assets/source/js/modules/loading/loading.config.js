(function(){
    'use strict';

    angular
        .module('app.loading')
        .config(function($httpProvider){
            $httpProvider.interceptors.push('LoadingInterceptor');
        });

})();