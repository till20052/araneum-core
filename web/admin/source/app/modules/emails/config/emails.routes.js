(function () {
    'use strict';

    angular
        .module('app.emails')
        .config(routes);

    routes.$inject = ['$stateProvider', 'RouteHelpersProvider'];

    /**
     * Routes of Emails
     *
     * @param $stateProvider
     * @param helper
     */
    function routes($stateProvider, helper) {
        $stateProvider
            .state('app.emails', {
                url: '/emails',
                initialize: '/manage/mails/init.json',
                crud: {
                    icon: 'icon-envelope',
                    title: 'admin.emails.TITLE'
                },
                templateUrl: helper.basepath('crud.html'),
                resolve: helper.resolveFor('datatables', 'oitozero.ngSweetAlert', 'ui.select')
            });
    }

})();