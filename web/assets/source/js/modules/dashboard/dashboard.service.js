(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .service('DashboardService', DashboardService);

    DashboardService.$inject = ['$http'];

    function DashboardService($http) {
        var spinkitCss = '/assets/vendor/spinkit/css/spinkit.css';

        var service = {
            appendSpinkit: appendSpinkit
        };

        return service;

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
        }
    }

})();