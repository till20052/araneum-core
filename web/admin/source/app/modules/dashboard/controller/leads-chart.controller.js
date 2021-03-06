(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('LeadsChartController', LeadsChartController);

    LeadsChartController.$inject = ['DashboardService', '$rootScope', '$translate'];

    /**
     * Leads chart controller
     *
     * @param DashboardService
     * @param $rootScope
     * @param $translate
     * @constructor
     */
    function LeadsChartController(DashboardService, $rootScope, $translate) {
        /* jshint validthis: true */
        var vm = this;

        /** @typedef {Number} */
        vm.count = 0;

        /** @typeof {Boolean} */
        vm.inLoading = false;

        /** @typedef {Object|{data: Array<Array>}} */
        vm.chart = {
            data: [],
            options: {
                series: {
                    lines: {
                        show: true,
                        fill: 0.8
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
                    tickColor: '#fcfcfc',
                    mode: 'categories'
                },
                yaxis: {
                    min: 0,
                    tickDecimals: 0,
                    tickColor: '#eee',
                    position: ($rootScope.app.layout.isRTL ? 'right' : 'left'),
                    tickFormatter: function (v) {
                        return v + ' ' + vm.captions.leads;
                    }
                },
                shadowSize: 0
            }
        };

        vm.captions = {
            leads: 'leads'
        };

        DashboardService.onDataLoaded(
            /**
             * Invoked in case if data was successfully loaded
             *
             * @param {{data: {charts: {leads: {count: Number, data: Array<Array>}}}}} response
             */
            function (response) {
                var chart = response.data.charts.leads;
                vm.count = chart.count;
                assignChartData(vm.chart, chart.data);
            }
        );

        /**
         * Assign data to chart
         *
         * @param chart
         * @param data
         */
        function assignChartData(chart, data) {
            chart.data = DashboardService.assignColorsByLabel(data);
        }

        $translate('admin.dashboard.widget.LEADS').then(function (value) {
            vm.captions.leads = value;
        });

    }

})();