(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('DashboardController', DashboardController);

    DashboardController.$inject = ['$scope', 'ChartData', '$timeout', 'DashboardService', 'DashboardFactory'];
    function DashboardController($scope, ChartData, $timeout, DashboardService, DashboardFactory) {
        var vm = this;

        $scope.child = {};
        $scope.statistics = {};


        activate();

        ////////////////

        function activate() {

            DashboardService
                .appendSpinkit();

            DashboardFactory.getStats().then(function(data){
                $scope.statistics = data.statistics;
            }, function(res){
                console.log(res);
            });

            // PANEL REFRESH EVENTS
            // -----------------------------------
            $scope.$on('panel-refresh', function (event, id) {
                DashboardFactory.refreshStats().then(function(data){
                    $scope.statistics = data.statistics;
                    $scope.$broadcast('removeSpinner', id);
                }, function(res){
                    console.log(res);
                });
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