(function (angular) {
	'use strict';

	angular
		.module('araneum')
		.service('UserAuthService', ['$http', UserAuthService]);

	function UserAuthService($http) {

		return {
			getAuthorizedUserData: getAuthorizedUserData
		};

		function getAuthorizedUserData(callback) {
			$http
				.get('/en/user/profile/get_authorized_user_data')
				.success(function (response) {
					callback(response);
				});
		}

	}

})(angular);

