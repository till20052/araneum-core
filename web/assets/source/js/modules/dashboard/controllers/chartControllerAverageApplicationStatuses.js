(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('ChartAverageApplicationStatusesController', ChartAverageApplicationStatusesController);

    ChartAverageApplicationStatusesController.$inject = ['$scope', 'DashboardService'];

    function ChartAverageApplicationStatusesController($scope, DashboardService) {

        DashboardService.getStats().then(function(data){
            $scope.statistics = data.statistics;
            console.log($scope.statistics);
            activate();
        }, function(res){
            console.log(res);
        });

        function activate() {
            // Line chart
            // -----------------------------------
            $scope.lineData =[{
                "label": "Success",
                "color": "#27c24c",
                "data": $scope.statistics.daylyAverageStatuses.success
            }, {
                "label": "Problem",
                "color": "#ff902b",
                "data": $scope.statistics.daylyAverageStatuses.problems
            }, {
                "label": "Error",
                "color": "#f05050",
                "data": $scope.statistics.daylyAverageStatuses.errors
            }, {
                "label": "Disabled",
                "color": "#dde6e9",
                "data": $scope.statistics.daylyAverageStatuses.disabled
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
                    content: function (label, x, y) { return x + ' : ' + y; }
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
        }
    }
})();
