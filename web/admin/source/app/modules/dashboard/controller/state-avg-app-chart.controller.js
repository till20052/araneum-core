(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('StateAvgAppChartController', StateAvgAppChartController);

    StateAvgAppChartController.$inject = ['$scope', 'DashboardService'];

	/**
     * State Average Application Chart Controller
     *
     * @param $scope
     * @param DashboardService
     * @constructor
     */
    function StateAvgAppChartController($scope, DashboardService) {

        /**
         * Constructor
         */
        (function () {
            $scope.lineData = [{
                "label": "Success",
                "color": "#27c24c",
                "data": []
            }, {
                "label": "Problem",
                "color": "#ff902b",
                "data": []
            }, {
                "label": "Error",
                "color": "#f05050",
                "data": []
            }, {
                "label": "Disabled",
                "color": "#dde6e9",
                "data": []
            }
            ];

            $scope.lineOptions = {
                series: {
                    lines: {
                        show: true,
                        fill: 0.01
                    },
                    points: {
                        show: true,
                        radius: 4
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
                    tickColor: '#eee',
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

                angular.forEach(['success', 'problems', 'errors', 'disabled'], function(value, i){
                    this[i].data = response.data.statistics.daylyAverageStatuses[value];
                }, $scope.lineData);

            }, function (error) {
                $scope.onLoading = false;
                $scope.errors.push('No data load: '+error.statusText);
            });
        })();

    }
})();
