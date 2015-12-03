(function (ng) {
	'use strict';

	ng.module('app.dashboard')
		.controller('SummaryDashboardController', SummaryDashboardController);

	SummaryDashboardController.$inject = ['$scope', 'DashboardService'];

	/**
	 * Summary Dashboard Controller
	 *
	 * @param $scope
	 * @param DashboardService
	 * @constructor
	 */
	function SummaryDashboardController($scope, DashboardService) {

		/**
		 * Controller
		 */
		(function(vm){

			vm.error = '';
			vm.statistics = {
				applications: {
					name: 'admin.dashboard.wiget.APPLICATIONS',
					icon: 'screen-tablet'
				},
				clusters: {
					name: 'admin.dashboard.wiget.CLUSTERS',
					icon: 'grid'
				},
				admins: {
					name: 'admin.dashboard.wiget.ADMINS',
					icon: 'users'
				},
				connections: {
					name: 'admin.dashboard.wiget.CONNECTIONS',
					icon: 'share-alt'
				},
				locales: {
					name: 'admin.dashboard.wiget.LOCALES',
					icon: 'globe-alt'
				}
			};

			DashboardService.onDataLoaded(function(response){
				ng.forEach(response.data.statistics.summary, function(value, key){
					if(typeof this[key] != 'undefined'){
						this[key].value = value;
					}
				}, vm.statistics);
			}, function(error){
				vm.error = error
			});

		})($scope);

	}

})(angular);