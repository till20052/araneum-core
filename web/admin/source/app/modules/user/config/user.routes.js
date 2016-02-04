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
                initialize: '/manage/users/init.json',
                controller: 'CRUDController',
                controllerAs: 'crud',
                crud: {
                    icon: 'icon-people',
                    title: 'admin.users.TITLE'
                },
                templateUrl: helper.basepath('crud.html'),
                resolve: helper.resolveFor('ngDialog', 'datatables', 'oitozero.ngSweetAlert', 'whirl')
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

