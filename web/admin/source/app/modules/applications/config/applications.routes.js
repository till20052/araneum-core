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
                templateUrl: helper.basepath('grid-template.html'),
                resolve: angular.extend(helper.resolveFor('ngDialog', 'datatables', 'localytics.directives', 'oitozero.ngSweetAlert', 'whirl', 'toaster'))
            });
    }
})();