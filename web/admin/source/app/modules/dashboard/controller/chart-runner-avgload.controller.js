(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('ChartRunnerAvgloadController', ChartRunnerAvgloadController);

    ChartRunnerAvgloadController.$inject = ['DashboardService', '$scope'];

    /**
     * Chart of Runner's Average Load

     * @param DashboardService
     * @constructor
     */
    function ChartRunnerAvgloadController(DashboardService) {
        /* jshint validthis: true */
        var vm = this;

        vm.data = [];
        vm.options = {
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
                tickFormatter: function (v) {
                    return v + '%';
                }
            },
            shadowSize: 0
        };

        DashboardService.onDataLoaded(
            /**
             * Invoked in case if dashboard data was successfully loaded
             *
             * @param {{
             *  data: {
             *      charts: {
             *          runners: {
             *              avgload: Array<Object>
             *          }
             *      }
             *  }
             * }} response
             */
            function (response) {
                vm.data = response.data.charts.runners.avgload;
            }
        );

    }

})();
