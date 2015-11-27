(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('UpTimeClusterController', UpTimeClusterController);

    UpTimeClusterController.$inject = ['$scope', 'DashboardService'];

    function UpTimeClusterController($scope, DashboardService) {

        activate();

        function activate() {

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
                    content: function (label, x, y) { return x + ' : ' + y; }
                },
                xaxis: {
                    tickColor: '#fcfcfc',
                    mode: 'categories'
                },
                yaxis: {
                    min: 0,
                    max: 200, // optional: use it for a clear represetation
                    position: ($scope.app.layout.isRTL ? 'right' : 'left'),
                    tickColor: '#eee'
                },
                shadowSize: 0
            };

            $scope.errors = [];

            $scope.onLoading = true;

            DashboardService.getStats().then(function (data) {
                $scope.onLoading = false;
                $scope.barData=data.statistics.clusterUpTime;
                console.log($scope.barData);
            }, function (res) {
                $scope.onLoading = false;
                $scope.errors.push('No data load:'+res.statusText);
            });
        }

    }
})();
