(function () {
    'use strict';

    angular
        .module('app.users')
        .config(routes);

    routes.$inject = ['$stateProvider', 'RouteHelpersProvider'];

    /**
     * Routes of Users
     *
     * @param $stateProvider
     * @param helper
     */
    function routes($stateProvider, helper) {
        $stateProvider
            .state('app.users', {
                url: '/users',
                initialize: '/manage/users/init.json',
                crud: {
                    icon: 'icon-people',
                    title: 'admin.users.TITLE'
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

