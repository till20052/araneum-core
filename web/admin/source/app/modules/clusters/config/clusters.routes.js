/**=========================================================
 * Module: routes.config.js
 * App routes and resources configuration
 =========================================================*/


(function () {
    'use strict';

    angular
        .module('app.clusters')
        .config(routesConfig);

    routesConfig.$inject = ['$stateProvider', 'RouteHelpersProvider'];
    function routesConfig($stateProvider, helper) {
        $stateProvider
            .state('app.clusters', {
                url: '/clusters',
                initialize: '/manage/clusters/init.json',
                crud: {
                    icon: 'icon-globe-alt',
                    title: 'clusters.CLUSTERS'
                },
                templateUrl: helper.basepath('crud.html'),
                resolve: helper.resolveFor('datatables', 'oitozero.ngSweetAlert', 'ui.select')
            });
    }
})();