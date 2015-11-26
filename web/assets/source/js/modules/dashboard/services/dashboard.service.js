(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .service('DashboardService', DashboardService);

    DashboardService.$inject = ['$http', '$q'];

    function DashboardService($http, $q) {
        var spinkitCss = '/assets/vendor/spinkit/css/spinkit.css';
        var dataSourceUrl = '/manage/dashboard/data-source.json';

        var data = [],
            lastRequestFailed = true,
            promise;

        promise = $http.get(dataSourceUrl)
            .then(function(res) {
                lastRequestFailed = false;
                data = res.data;
                return data;
            });


        var service = {
            appendSpinkit: appendSpinkit,
            refreshStats: function() {
                // $http returns a promise, so we don't need to create one with $q
                promise = $http.get(dataSourceUrl)
                    .then(function(res) {
                        lastRequestFailed = false;
                        data = res.data;
                        return data;
                    }, function(res) {
                        return $q.reject(res);
                    });
                return promise;
            },
            getStats: function(){
                return promise;
            }
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