/**=========================================================
 * Module: config.js
 * App routes and resources configuration
 =========================================================*/


(function () {
    'use strict';

    angular
        .module('app.routes')
        .config(routesConfig);

    routesConfig.$inject = ['$stateProvider', '$locationProvider', '$urlRouterProvider', 'RouteHelpersProvider'];
    function routesConfig($stateProvider, $locationProvider, $urlRouterProvider, helper) {

        // Set the following to true to enable the HTML5 Mode
        // You may have to set <base> tag in index and a routing configuration in your server
        $locationProvider.html5Mode(true);

        // defaults to authorization
        $urlRouterProvider.otherwise('/en/manage/login');

        //
        // Application Routes
        // -----------------------------------
        $stateProvider
            .state('app', {
                url: '/en/manage',
                abstract: true,
                templateUrl: helper.basepath('app.html'),
                resolve: helper.resolveFor('fastclick', 'modernizr', 'icons', 'screenfull', 'animo', 'slimscroll', 'classyloader', 'toaster', 'whirl')
            })
            .state('app.main', {
                url: '/dashboard',
                title: 'Main page',
                templateUrl: helper.basepath('dashboard.html'),
                resolve: helper.resolveFor('flot-chart','flot-chart-plugins', 'chartjs')
            })
            .state('login', {
                url: '/en/manage/login',
                title: 'Authorization',
                templateUrl: '/en/user/login.html'
            })
            .state('recover', {
                url: '/en/manage/recover',
                title: 'Recover',
                templateUrl: helper.basepath('recover.html'),
                resolve: helper.resolveFor('whirl')
            })
            .state('reset', {
                url: '/en/manage/recover/{token}',
                title: 'Recover',
                templateUrl: helper.basepath('recover.html'),
                resolve: helper.resolveFor('whirl')
            })
    }

})();

