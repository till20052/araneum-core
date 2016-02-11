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
                crud: {
                    icon: 'icon-globe-alt',
                    title: 'admin.locales.TITLE'
                },
                templateUrl: helper.basepath('crud.html'),
                resolve: helper.resolveFor('ngDialog', 'datatables', 'oitozero.ngSweetAlert', 'whirl')
            });
    }

})();