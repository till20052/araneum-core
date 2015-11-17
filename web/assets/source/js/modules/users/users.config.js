(function (angular) {
	'use strict';

	angular
		.module('app.users')
		.config(['$httpProvider', function ($httpProvider) {
			$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
		}]);

})(angular);