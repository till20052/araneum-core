(function () {
    'use strict';

    angular
        .module('app')
        .config(coreConfig);

    coreConfig.$inject = ['$controllerProvider', '$compileProvider', '$filterProvider', '$provide', 'HTTPEventListenerProvider'];
    function coreConfig($controllerProvider, $compileProvider, $filterProvider, $provide, HTTPEventListenerProvider) {

        var core = angular.module('app');
        // registering components after bootstrap
        core.controller = $controllerProvider.register;
        core.directive = $compileProvider.directive;
        core.filter = $filterProvider.register;
        core.factory = $provide.factory;
        core.service = $provide.service;
        core.constant = $provide.constant;
        core.value = $provide.value;

        HTTPEventListenerProvider.onError(function(httpEvent){
            if(httpEvent.content.status == 401){
				httpEvent.state.go('login');
			}
        });

    }

})();