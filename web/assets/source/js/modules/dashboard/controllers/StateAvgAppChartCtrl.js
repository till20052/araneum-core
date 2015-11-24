(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('StateAvgAppChartCtrl', StateAvgAppChartCtrl);

    StateAvgAppChartCtrl.$inject = ['$scope', 'DashboardService'];

    function StateAvgAppChartCtrl($scope, DashboardService) {

        activate();

        function activate() {
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
                    position: ($scope.app.layout.isRTL ? 'right' : 'left'),
                    tickColor: '#eee'
                },
                shadowSize: 0
            };

            DashboardService.getStats().then(function (data) {
                    $scope.lineData[0].data = data.statistics.daylyAverageStatuses.errors;
                    $scope.lineData[1].data = data.statistics.daylyAverageStatuses.problems;
                    $scope.lineData[2].data = data.statistics.daylyAverageStatuses.success;
                    $scope.lineData[3].data = data.statistics.daylyAverageStatuses.disabled;
            }, function (res) {
                console.log(res);
            });
        }

    }
})();
