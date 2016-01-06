/**=========================================================
 * Module: routes.config.js
 * App routes and resources configuration
 =========================================================*/


(function () {
    'use strict';

    angular
        .module('app.users')
        .config(routesConfig);

    routesConfig.$inject = ['$stateProvider', 'RouteHelpersProvider'];
    function routesConfig($stateProvider, helper) {
        $stateProvider
            .state('app.users', {
                url: '/users',
                title: 'Users',
                initialize: '/manage/users/init.json',
                templateUrl: helper.basepath('grid-template.html'),
                resolve: angular.extend(helper.resolveFor('ngDialog', 'datatables', 'localytics.directives', 'oitozero.ngSweetAlert', 'whirl', 'toaster')),
                controller: 'UserTableController'
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

