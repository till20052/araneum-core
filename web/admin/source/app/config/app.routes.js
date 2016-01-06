/**=========================================================
 * Module: config.js
 * App routes and resources configuration
 =========================================================*/

(function () {
    'use strict';

    angular
        .module('app')
        .config(routesConfig);

    routesConfig.$inject = ['$stateProvider', '$locationProvider', '$urlRouterProvider', 'RouteHelpersProvider'];
    function routesConfig($stateProvider, $LocationProvider, $urlRouterProvider, helper) {
        $LocationProvider.html5Mode(true);
        $urlRouterProvider.otherwise('/dashboard');
        $stateProvider
            .state('app', {
                abstract: true,
                templateUrl: helper.basepath('app.html'),
                resolve: helper.resolveFor('ngDialog', 'fastclick', 'modernizr', 'icons', 'screenfull', 'animo', 'slimscroll', 'classyloader', 'toaster', 'whirl')
            });
    }

})();

