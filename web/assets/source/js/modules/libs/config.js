(function(angular){

	'use strict';

	angular
		.module('app.libs')
		.config(['$httpProvider', config]);

	function config($httpProvider){
		$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
	}

})(angular);