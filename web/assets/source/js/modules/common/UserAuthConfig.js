(function(){

	'use strict';

	angular
		.module('araneum')
		.config(['$httpProvider', UserAuthConfig]);

	function UserAuthConfig($httpProvider){
		$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
	}

})();