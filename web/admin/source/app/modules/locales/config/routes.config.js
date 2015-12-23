/**=========================================================
 * Module: routes.config.js
 * App routes and resources configuration
 =========================================================*/


(function () {
    'use strict';

    angular
        .module('app')
        .config(routesConfig);

    routesConfig.$inject = ['$stateProvider', 'RouteHelpersProvider'];
    function routesConfig($stateProvider, helper) {
        $stateProvider
            .state('app.locales', {
                url: '/locales',
                initialize: '/manage/locales/init.json',
                templateUrl: helper.basepath('grid-template.html'),
                resolve: helper.resolveFor('datatables', 'whirl'),
                controller: 'LocalesController'
            })
    }

})();