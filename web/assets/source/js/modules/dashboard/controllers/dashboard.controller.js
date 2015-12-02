(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('DashboardController', DashboardController);

    DashboardController.$inject = ['$scope', 'DashboardService'];
    function DashboardController($scope, DashboardService) {
        var vm = this;

        activate();

        function activate() {

            DashboardService
                .appendSpinkit()
				.onDataLoaded(function(response){
					$scope.statistics = response.data.statistics;
				})
				.loadData();

            $scope.$on('panel-refresh', function (event, id) {
				DashboardService.refreshData(function(){
					$scope.$broadcast('removeSpinner', id);
				});
            });

            $scope.$on('panel-remove', function (event, id, deferred) {
                console.log('Panel #' + id + ' removing');
                deferred.resolve();
            });

            $scope.$on('panel-removed', function (event, id) {
                console.log('Panel #' + id + ' removed');
            });
        }
    }
})();