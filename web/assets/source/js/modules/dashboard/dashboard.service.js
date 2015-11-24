(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .service('DashboardService', DashboardService);

    DashboardService.$inject = ['$http'];
    function DashboardService($http) {
        var spinkitCss = '/assets/vendor/spinkit/css/spinkit.css';
        var dataSourceUrl = '/manage/dashboard/data_source.json';

        var service = {
            loadDataSource: loadDataSource,
            appendSpinkit: appendSpinkit
        };

        return service;

        function loadDataSource(onSuccess, onError) {
            onError = onError || function () {
                    console.log('Failure loading dashboard data');
                };

            $http
                .get(dataSourceUrl)
                .success(onSuccess)
                .error(onError);

            return service;
        };

        function appendSpinkit () {
            if ( ! $('link[href="' + spinkitCss + '"]').length > 0) {
                angular.element('head')
                    .append(
                        $('<link />')
                            .attr({
                                rel: 'stylesheet',
                                type: 'text/css',
                                href: spinkitCss
                            })
                    );
            }

            return service;
        };
    }

})();