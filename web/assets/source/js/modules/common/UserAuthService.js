(function (angular) {

	'use strict';

	angular
		.module('araneum')
		.service('UserAuth', ['$state', '$cookies', UserAuth]);

	function UserAuth($state, $cookies) {

		return {
			getUser: getUser,
			isUserAuthorized: isUserAuthorized,
			checkAccess: checkAccess
		};

		function getUser() {
			return JSON.parse($cookies.get('user') || false);
		}

		function isUserAuthorized() {
			return typeof getUser() == 'object';
		}

		function checkAccess(event, toState) {
			if ($.inArray(toState.name, ['login', 'resetting', 'reset']) >= 0) {
				if (!isUserAuthorized()) {
					return;
				}
				event.preventDefault();
				$state.go('app.main');
			} else if (!isUserAuthorized()) {
				event.preventDefault();
				$state.go('login');
			}
		}

	}

})(angular);

