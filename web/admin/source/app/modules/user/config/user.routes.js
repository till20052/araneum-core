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
                crud: {
                    icon: 'icon-users',
                    title: 'admin.sidebar.nav.USERS'
                },
                templateUrl: helper.basepath('crud.html'),
                resolve: helper.resolveFor('admin.crud', 'datatables', 'oitozero.ngSweetAlert', 'ui.select')
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

