(function (angular) {

	'use strict';

	angular
		.module('araneum')
		.service('UserAuth', ['$http', 'User', '$state', '$rootScope', UserAuth]);

	function UserAuth($http, User, $state, $rootScope) {

		var _csrf_token;

		$rootScope.$on('$stateChangeStart', function (event, toState) {
			if( ! User.isAuthorized() && toState.name != 'login'){
				event.preventDefault();
				$state.go('login');
			}
		});

		return {
			init: init,
			login: login,
			logout: logout,
			hasAccess: hasAccess
		};

		function init() {
			$http
				.get('/en/login')
				.success(function (response) {
					_csrf_token = response;
				});
		}

		function login(data) {
			$http
				.post('/en/login_check', $.param({
						_username: data.username,
						_password: data.password,
						_csrf_token: _csrf_token
					}),
					{
						headers: {'Content-Type': 'application/x-www-form-urlencoded'}
					})
				.success(function (response) {
					var event = createEvent(response);

					if(typeof data.onSuccess == 'function'){
						data.onSuccess(event);
					}

					if( ! event.isPropagationStopped()){
						User.data($.extend(response, {isAuthorized: true}));
						$state.go('app.dashboard');
					}
				})
				.error(function (response) {
					var event = createEvent(response);

					if(typeof data.onError == 'function'){
						data.onError(event);
					}

					if( ! event.isPropagationStopped()){
						_csrf_token = response._csrf_token;
					}
				});
		}

		function logout() {

		}

		function hasAccess() {

		}

		function createEvent(response){
			var isEventPropagationStopped = false;

			return {
				response: response,
				isPropagationStopped: isPropagationStopped,
				stopPropagation: stopPropagation
			};

			function stopPropagation(){
				isEventPropagationStopped = true;
			}

			function isPropagationStopped(){
				return isEventPropagationStopped;
			}
		}

	}

})(angular);

