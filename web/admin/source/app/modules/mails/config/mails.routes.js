/**=========================================================
 * Module: routes.config.js
 * App routes and resources configuration
 =========================================================*/


(function () {
    'use strict';

    angular
        .module('app.mails')
        .config(routesConfig);

    routesConfig.$inject = ['$stateProvider', 'RouteHelpersProvider'];
    function routesConfig($stateProvider, helper) {
        $stateProvider
            .state('app.mails', {
                url: '/mails',
                initialize: '/manage/mails/init.json',
                crud: {
                    icon: 'icon-globe-alt',
                    title: 'mails.MAILS'
                },
                templateUrl: helper.basepath('crud.html'),
                resolve: helper.resolveFor('datatables', 'oitozero.ngSweetAlert', 'ui.select')
            });
    }
})();