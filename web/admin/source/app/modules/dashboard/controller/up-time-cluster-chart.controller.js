(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('UpTimeClusterController', UpTimeClusterController);

    UpTimeClusterController.$inject = ['$scope', 'DashboardService'];

    /**
     * Up Time Cluster Controller
     *
     * @param $scope
     * @param DashboardService
     * @constructor
     */
    function UpTimeClusterController($scope, DashboardService) {

        /**
         * Constructor
         */
        (function () {

            $scope.barData = [];
            $scope.barOptions = {
                series: {
                    stack: true,
                    bars: {
                        align: 'center',
                        lineWidth: 0,
                        show: true,
                        barWidth: 0.6,
                        fill: 0.9
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
                    position: ($scope.app.layout.isRTL ? 'right' : 'left'),
                    tickColor: '#eee',
                    tickDecimals: 0
                },
                shadowSize: 0
            };

            $scope.errors = [];

            $scope.onLoading = true;

            DashboardService.onDataLoaded(function (response) {
                $scope.onLoading = false;
                $scope.barData = response.data.statistics.clusterUpTime;
            }, function (error) {
                $scope.onLoading = false;
                $scope.errors.push('No data load:' + error.statusText);
            });
        })();

    }
})();
