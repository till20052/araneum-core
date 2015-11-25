(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('AppStatusesChartCtrl', AppStatusesChartCtrl);

    AppStatusesChartCtrl.$inject = ['Colors', '$scope', 'DashboardService'];

    function AppStatusesChartCtrl(Colors, $scope, DashboardService) {
        activate();

        function activate(){
            $scope.barData = {
                labels:  [],
                datasets : [
                    {
                        label: 'Error',
                        fillColor : Colors.byName('danger'),
                        strokeColor : Colors.byName('danger'),
                        highlightFill: Colors.byName('danger'),
                        highlightStroke: Colors.byName('danger'),
                        data : []
                    },
                    {
                        label: 'Success',
                        fillColor : Colors.byName('success'),
                        strokeColor : Colors.byName('success'),
                        highlightFill : Colors.byName('success'),
                        highlightStroke : Colors.byName('success'),
                        data : []
                    },
                    {
                        label: 'Warning',
                        fillColor : Colors.byName('warning'),
                        strokeColor : Colors.byName('warning'),
                        highlightFill : Colors.byName('warning'),
                        highlightStroke : Colors.byName('warning'),
                        data : []
                    },
                    {
                        label: 'Disabled',
                        fillColor : Colors.byName('gray'),
                        strokeColor : Colors.byName('gray'),
                        highlightFill : Colors.byName('gray'),
                        highlightStroke : Colors.byName('gray'),
                        data : []
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

            $scope.errors=[];

            DashboardService.getStats().then(function(data){

                $scope.$broadcast('removeSpinner', 'panelChart5');
                $scope.barData.labels = data.statistics.daylyApplications.applications;

                angular.forEach(['errors', 'success', 'problems', 'disabled'], function(value, i){
                    this[i].data = data.statistics.daylyApplications[value];
                }, $scope.barData.datasets);

            }, function(res){
                $scope.errors[0] = 'No data load:' + res;
            });
        }
    }


})();
