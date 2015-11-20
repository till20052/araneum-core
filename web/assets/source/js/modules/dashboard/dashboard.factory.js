(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .factory('DashboardFactory', DashboardFactory);

    DashboardFactory.$injector = ['$http', '$q'];

    function DashboardFactory($http, $q){
        var dataSourceUrl = '/manage/dashboard/data_source.json';

        var data = [],
            lastRequestFailed = true,
            promise;

        promise = $http.get(dataSourceUrl)
            .then(function(res) {
                lastRequestFailed = false;
                data = res.data;
                return data;
            });

        return {
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
        }


    }

})();