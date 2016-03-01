(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('ClusterLoadAverageController', ClusterLoadAverageController);

    ClusterLoadAverageController.$inject = ['DashboardService', '$scope'];

    /**
     * Cluster Load Average Controller

     * @param DashboardService
     * @param $scope
     * @constructor
     */
    function ClusterLoadAverageController(DashboardService, $scope) {

        /**
         * Constructor
         */
        (function () {

            $scope.splineData = [];

            $scope.splineOptions = {
                series: {
                    lines: {
                        show: false
                    },
                    points: {
                        show: true,
                        radius: 4
                    },
                    splines: {
                        show: true,
                        tension: 0.4,
                        lineWidth: 1,
                        fill: 0.5
                    }
                },
                grid: {
                    borderColor: '#eee',
                    borderWidth: 1,
                    hoverable: true,
                    backgroundColor: '#fcfcfc'
                },
                tooltip: true,
                tooltipOpts: {
                    content: function (label, x, y) {
                        return x + ' : ' + y;
                    }
                },
                xaxis: {
                    tickColor: '#fcfcfc',
                    mode: 'categories'
                },
                yaxis: {
                    min: 0,
                    max: 100,
                    tickColor: '#eee',
                    position: ($scope.app.layout.isRTL ? 'right' : 'left'),
                    tickFormatter: function (v) {
                        return v + '%';
                    }
                },
                shadowSize: 0
            };

            $scope.errors = [];

            $scope.onLoading = true;

            DashboardService.onDataLoaded(function (response) {
                var data = response.data;
                $scope.onLoading = false;
                $scope.splineData = data.statistics.clusterLoadAverage;
            }, function (error) {
                $scope.onLoading = false;
                $scope.errors.push('No data load:' + error.statusText);
            });
        })();

    }
})();
