(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('ChartRunnerUptimeController', ChartRunnerUptimeController);

    ChartRunnerUptimeController.$inject = ['DashboardService'];

    /**
     * Chart Runner Uptime Controller
     *
     * @param DashboardService
     * @constructor
     */
    function ChartRunnerUptimeController(DashboardService) {
        /* jshint validthis: true */
        var vm = this;

        vm.data = [];
        vm.options = {
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
                tickColor: '#eee',
                tickDecimals: 0
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
             *              uptime: Array<Object>
             *          }
             *      }
             *  }
             * }} response
             */
            function (response) {
                vm.data = response.data.charts.runners.uptime;
            }
        );

    }

})();
