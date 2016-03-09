/**=========================================================
 * Module: routes.config.js
 * App routes and resources configuration
 =========================================================*/


(function () {
    'use strict';

    angular
        .module('app.applications')
        .config(routesConfig);

    routesConfig.$inject = ['$stateProvider', 'RouteHelpersProvider'];
    function routesConfig($stateProvider, helper) {
        $stateProvider
            .state('app.applications', {
                url: '/applications',
                initialize: '/manage/applications/init.json',
                crud: {
                    icon: 'icon-globe-alt',
                    title: 'leads.LEADS'
                },
                templateUrl: helper.basepath('crud.html'),
                resolve: helper.resolveFor('datatables', 'oitozero.ngSweetAlert', 'ui.select')
            });
    }
})();