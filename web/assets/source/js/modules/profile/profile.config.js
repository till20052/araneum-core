(function (angular) {
	'use strict';

	angular
		.module('app.profile')
		.config(['$httpProvider', profileConfig]);

	function profileConfig($httpProvider) {
		$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
	}

})(angular);