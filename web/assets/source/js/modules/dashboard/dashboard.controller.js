(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('DashboardController', DashboardController);

    DashboardController.$inject = ['$scope', 'ChartData', '$timeout', 'DashboardService'];
    function DashboardController($scope, ChartData, $timeout, DashboardService) {
        var vm = this;

        $scope.child = {};
        $scope.statistics = {};


        activate();

        ////////////////

        function activate() {

            DashboardService
                .appendSpinkit()
                .loadDataSource(function (dataSource) {
                    $scope.statistics = dataSource.statistics;
                });

            // PANEL REFRESH EVENTS
            // -----------------------------------

            $scope.$on('panel-refresh', function (event, id) {

                console.log('Simulating chart refresh during 3s on #' + id);

                // Instead of timeout you can request a chart data
                $timeout(function () {

                    // directive listen for to remove the spinner
                    // after we end up to perform own operations
                    $scope.$broadcast('removeSpinner', id);

                    console.log('Refreshed #' + id);

                }, 3000);

            });


            // PANEL DISMISS EVENTS
            // -----------------------------------

            // Before remove panel
            $scope.$on('panel-remove', function (event, id, deferred) {

                console.log('Panel #' + id + ' removing');

                // Here is obligatory to call the resolve() if we pretend to remove the panel finally
                // Not calling resolve() will NOT remove the panel
                // It's up to your app to decide if panel should be removed or not
                deferred.resolve();

            });

            // Panel removed ( only if above was resolved() )
            $scope.$on('panel-removed', function (event, id) {

                console.log('Panel #' + id + ' removed');

            });

        }
    }
})();