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
            .state('app.users', {
                url: '/users',
                title: 'Users',
                initialize: '/user/manage/users/init.json',
                templateUrl: helper.basepath('grid-template.html'),
                resolve: helper.resolveFor('datatables', 'whirl'),
                controller: 'UserTableController'
            })
            .state('app.locales', {
                url: '/locales',
                title: 'Locales',
                initialize: '/user/manage/locales/init.json',
                templateUrl: helper.basepath('grid-template.html'),
                resolve: helper.resolveFor('datatables', 'whirl'),
                controller: 'LocalesController'
            })
            .state('login', {
                url: '/login',
                title: 'Authorization',
                templateUrl: helper.basepath('users/login.html'),
                resolve: helper.resolveFor('whirl'),
                defaultState: 'app.dashboard'
            })
            .state('resetting', {
                url: '/resetting',
                title: 'Recover',
                templateUrl: helper.basepath('users/resettingBase.html'),
                resolve: helper.resolveFor('whirl')
            })
            .state('reset', {
                url: '/resetting/reset/{token}',
                title: 'Recover',
                templateUrl: helper.basepath('users/resettingBase.html'),
                resolve: helper.resolveFor('whirl')
            });
    }

})();

