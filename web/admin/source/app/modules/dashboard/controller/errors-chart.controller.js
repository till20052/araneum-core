(function () {
    'use strict';

    angular.module('app.dashboard')
        .controller('ErrorsChartController', ErrorsChartController);

    ErrorsChartController.$inject = ['DashboardService', '$rootScope', '$interpolate'];

    /**
     * Controller of errors chart
     *
     * @param dashboard
     * @param $rootScope
     * @constructor
     */
    function ErrorsChartController(dashboard, $rootScope, $interpolate) {
        /* jshint validthis: true,
         eqeqeq: false,
         -W083 */
        var vm = this;

        /** @typedef {Number} */
        vm.count = 0;

        /** @typedef {object|{data: Array<Array>}} */
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
                    content: function (label, x, y, p) {
                        var data = p.series.data,
                            hours = parseInt(data[data.length - 1][0]),
                            offset = p.dataIndex - 23;
                        return $interpolate('{{ label }}: {{ value }} {{ "admin.dashboard.widget.ERRORS" | translate }} ({{ date | date : \'d MMM HH:mm\' }})')({
                            label: label,
                            value: y,
                            date: (new Date()).setHours(hours + offset, 0)
                        });
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
                        return v + ' errors';
                    }
                },
                shadowSize: 0
            }
        };

        activate();

        /**
         * Activation
         */
        function activate() {
            dashboard.onDataLoaded(
                /**
                 * Invoked in case if data was successfully loaded
                 *
                 * @param {{
                 *  data: {
                 *      charts: {
                 *          errors: {
                 *              count: Number,
                 *              types: Object
                 *              data: Array<Object>
                 *          }
                 *      }
                 *  }
                 * }} response
                 */
                function (response) {
                    var errors = response.data.charts.errors,
                        lines = {},
                        data = [];

                    vm.count = errors.count;
                    errors.data.forEach(
                        /**
                         * @param {{sentAt: {date: String}, name: String}} token
                         */
                        function (token) {
                            if (typeof lines[token.name] == 'undefined') {
                                lines[token.name] = {
                                    label: token.name,
                                    data: getTimeRange()
                                };
                            }

                            lines[token.name]
                                .data[parseInt(getHours(token.sentAt.date))][1]++;
                        }
                    );

                    for (var key in lines) {
                        if (!lines.hasOwnProperty(key)) {
                            continue;
                        }
                        data.push(lines[key]);
                    }

                    vm.chart.data = dashboard.assignColorsByLabel(data);
                }
            );
        }

        /**
         * Get rounded hours from date
         *
         * @param {String|Date} date
         * @return {String}
         */
        function getHours(date) {
            var d = date;

            if (!(d instanceof Date)) {
                d = new Date(Date.parse(date));
            }

            d.setMinutes(d.getMinutes() + Math.round(d.getSeconds() / 60));
            d.setSeconds(0);

            d.setHours(d.getHours() + Math.round(d.getMinutes() / 60));
            d.setMinutes(0);

            return String('00' + d.getHours()).slice(-2);
        }

        /**
         * Get time range in 24 hours
         *
         * @returns {Array}
         */
        function getTimeRange() {
            var start = new Date(),
                end = new Date(),
                range = [];

            start.setHours(end.getHours() - 24);

            while (start < end) {
                range.push([getHours(start), 0]);
                start.setHours(start.getHours() + 1);
            }

            return range;
        }
    }

})();