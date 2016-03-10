(function () {
    'use strict';

    angular
        .module('app.applications')
        .config(routes);

    routes.$inject = ['$stateProvider', 'RouteHelpersProvider'];

    /**
     * Routes of Applications
     *
     * @param $stateProvider
     * @param helper
     */
    function routes($stateProvider, helper) {
        $stateProvider
            .state('app.applications', {
                url: '/applications',
                initialize: '/manage/applications/init.json',
                crud: {
                    icon: 'icon-calculator',
                    title: 'admin.applications.TITLE'
                },
                templateUrl: helper.basepath('crud.html'),
                resolve: helper.resolveFor('datatables', 'oitozero.ngSweetAlert', 'ui.select')
            });
    }

})();