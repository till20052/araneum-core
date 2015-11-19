(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('ChartDaylyApplicationStatusesController', ChartDaylyApplicationStatusesController);

    ChartDaylyApplicationStatusesController.$inject = ['Colors', '$scope', 'DashboardFactory'];

    function ChartDaylyApplicationStatusesController(Colors, $scope, DashboardFactory) {

        DashboardFactory.getStats().then(function(data){
            $scope.statistics = data.statistics;
            activate();
        }, function(res){
            console.log(res);
        });

        ////////////////

        function activate() {
            $scope.barData = {
                labels:  $scope.statistics.daylyApplications.applications,
                datasets : [
                    {
                        label: 'Error',
                        fillColor : Colors.byName('danger'),
                        strokeColor : Colors.byName('danger'),
                        highlightFill: Colors.byName('danger'),
                        highlightStroke: Colors.byName('danger'),
                        data : $scope.statistics.daylyApplications.errors
                    },
                    {
                        label: 'Success',
                        fillColor : Colors.byName('success'),
                        strokeColor : Colors.byName('success'),
                        highlightFill : Colors.byName('success'),
                        highlightStroke : Colors.byName('success'),
                        data : $scope.statistics.daylyApplications.success
                    },
                    {
                        label: 'Warning',
                        fillColor : Colors.byName('warning'),
                        strokeColor : Colors.byName('warning'),
                        highlightFill : Colors.byName('warning'),
                        highlightStroke : Colors.byName('warning'),
                        data : $scope.statistics.daylyApplications.problems
                    },
                    {
                        label: 'Disabled',
                        fillColor : Colors.byName('gray'),
                        strokeColor : Colors.byName('gray'),
                        highlightFill : Colors.byName('gray'),
                        highlightStroke : Colors.byName('gray'),
                        data : $scope.statistics.daylyApplications.disabled
                    }
                ]
            };

            $scope.barOptions = {
                scaleBeginAtZero: true,
                scaleShowGridLines: true,
                scaleGridLineColor: 'rgba(0,0,0,.05)',
                scaleGridLineWidth: 1,
                barShowStroke: true,
                barStrokeWidth: 2,
                barValueSpacing: 5,
                barDatasetSpacing: 1
            };
        }

    }


})();
