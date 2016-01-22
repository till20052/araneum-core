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
                templateUrl: helper.basepath('crud.html'),
                resolve: helper.resolveFor('ngDialog', 'datatables', 'localytics.directives', 'oitozero.ngSweetAlert', 'whirl', 'toaster')
            });
    }
})();