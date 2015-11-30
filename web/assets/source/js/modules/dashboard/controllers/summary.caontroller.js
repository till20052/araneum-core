(function (ng) {
	'use strict';

	ng.module('app.dashboard')
		.controller('SummaryDashboardController', SummaryDashboardController);

	SummaryDashboardController.$inject = ['$scope', 'DashboardService'];
	function SummaryDashboardController($scope, DashboardService) {

		/**
		 * Controller
		 */
		(function(vm){

			vm.error = '';
			vm.statistics = {
				applications: {
					name: 'Applications',
					icon: 'screen-tablet'
				},
				clusters: {
					name: 'Clusters',
					icon: 'grid'
				},
				admins: {
					name: 'Admins',
					icon: 'users'
				},
				connections: {
					name: 'Connections',
					icon: 'share-alt'
				},
				locales: {
					name: 'Locales',
					icon: 'globe-alt'
				}
			};

			DashboardService.getStats().then(function(response){
				ng.forEach(response.statistics.summary, function(value, key){
					if(typeof this[key] != 'undefined'){
						this[key].value = value;
					}
				}, vm.statistics);
			}).then(function(error){
				//vm.error = error
			});

		})($scope);

	}

})(angular);