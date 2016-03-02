(function () {
    'use strict';

    angular
        .module('app.locales')
        .config(routes);

    routes.$inject = ['$stateProvider', 'RouteHelpersProvider'];

    /**
     * Routes of Locales
     *
     * @param $stateProvider
     * @param helper
     */
    function routes($stateProvider, helper) {
        $stateProvider
            .state('app.locales', {
                url: '/locales',
                initialize: '/manage/locales/init.json',
                crud: {
                    icon: 'icon-globe-alt',
                    title: 'admin.locales.TITLE'
                },
                templateUrl: helper.basepath('crud.html'),
                resolve: helper.resolveFor('datatables', 'oitozero.ngSweetAlert', 'ui.select')
            });
    }

})();