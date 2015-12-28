(function () {
    'use strict';

    angular.module('app.dashboard')
        .controller('ErrorsChartController', ErrorsChartController);

    ErrorsChartController.$inject = ['$rootScope'];

    /**
     * Controller of errors chart
     *
     * @param $rootScope
     * @constructor
     */
    function ErrorsChartController($rootScope) {
        /* jshint validthis: true */
        var vm = this;

        /** @typedef {Number} */
        vm.count = 0;

        /** @typeof {Boolean} */
        vm.inLoading = false;

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
                    tickColor: '#eee',
                    position: ($rootScope.app.layout.isRTL ? 'right' : 'left'),
                    tickFormatter: function (v) {
                        return v.toFixed(1) + ' errors';
                    }
                },
                shadowSize: 0
            }
        };
    }

})();