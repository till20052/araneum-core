(function (ng) {
	'use strict';

	ng.module('app.dashboard')
		.controller('RegisteredCustomersController', RegisteredCustomersController);

	RegisteredCustomersController.$inject = ['$scope', 'DashboardService'];
	function RegisteredCustomersController($scope, DashboardService) {

		(function (vm) {

			vm.data = [];
			vm.options = {
				series: {
					lines: {
						show: true,
						fill: 0.8
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
					tickColor: '#fcfcfc',
					mode: 'categories'
				},
				yaxis: {
					min: 0,
					tickColor: '#eee',
					position: ($scope.app.layout.isRTL ? 'right' : 'left'),
					tickFormatter: function (v) {
						return v + ' visitors';
					}
				},
				shadowSize: 0
			};

			DashboardService.getStats().then(function(response){
				vm.data = response.statistics.registeredCustomers;
			});

		})($scope);

	}

})(angular);