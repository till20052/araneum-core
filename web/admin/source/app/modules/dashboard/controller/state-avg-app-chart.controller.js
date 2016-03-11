(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('StateAvgAppChartController', StateAvgAppChartController);

    StateAvgAppChartController.$inject = ['$scope', 'DashboardService', '$interpolate'];

	/**
     * State Average Application Chart Controller
     *
     * @param $scope
     * @param DashboardService
     * @constructor
     */
    function StateAvgAppChartController($scope, DashboardService, $interpolate) {

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
        }];

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
                content: function (label, x, y, p) {
                    var data = p.series.data,
                        hours = parseInt(data[data.length - 1][0]),
                        offset = p.dataIndex - 23;
                    return $interpolate('{{ label }}: {{ value }}% ({{ date | date : \'d MMM HH:mm\' }})')({
                        label: label,
                        value: y,
                        date: (new Date()).setHours(hours + offset, 0)
                    });
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
                tickDecimals: 0,
                tickFormatter: function (v) {
                    return v + '%';
                }
            },
            shadowSize: 0
        };

        $scope.errors = [];

        $scope.onLoading = true;

        DashboardService.onDataLoaded(function (response) {

            $scope.onLoading = false;

            angular.forEach(['success', 'problems', 'errors', 'disabled'], function (value, i) {
                this[i].data = response.data.statistics.daylyAverageStatuses[value];
            }, $scope.lineData);

        }, function (error) {
            $scope.onLoading = false;
            $scope.errors.push('No data load: ' + error.statusText);
        });

    }
})();
