/**=========================================================
 * Module: routes.config.js
 * App routes and resources configuration
 =========================================================*/


(function () {
    'use strict';

    angular
        .module('app.locales')
        .config(routesConfig);

    routesConfig.$inject = ['$stateProvider', 'RouteHelpersProvider'];
    function routesConfig($stateProvider, helper) {
        $stateProvider
            .state('app.locales', {
                url: '/locales',
                initialize: '/manage/locales/init.json',
                controller: 'CRUDController',
                controllerAs: 'crud',
                crud: {
                    icon: 'icon-globe-alt',
                    title: 'admin.locales.TITLE'
                },
                templateUrl: helper.basepath('crud.html'),
                resolve: helper.resolveFor('ngDialog', 'datatables', 'oitozero.ngSweetAlert', 'whirl')
            });
    }
})();