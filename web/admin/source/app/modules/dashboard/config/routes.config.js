/**=========================================================
 * Module: routes.config.js
 * App routes and resources configuration
 =========================================================*/


(function () {
    'use strict';

    angular
        .module('app')
        .config(routesConfig);

    routesConfig.$inject = ['$stateProvider', 'RouteHelpersProvider'];
    function routesConfig($stateProvider, helper) {
        $stateProvider
        $stateProvider
            .state('app.dashboard', {
                url: '/dashboard',
                title: 'Main page',
                templateUrl: helper.basepath('dashboard.html'),
                resolve: helper.resolveFor('flot-chart', 'flot-chart-plugins', 'chartjs', 'ngDialog'),
                controller: 'DashboardController'
            });
    }

})();