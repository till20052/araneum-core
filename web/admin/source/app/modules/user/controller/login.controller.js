(function (angular) {

	'use strict';

	angular
		.module('app.users')
		.controller('LoginController', ['$scope', 'UserAuth', LoginController]);

	function LoginController($scope, UserAuth) {

		(function (vm) {

			vm.inLoading = false;
			vm.submitDisabled = true;
			vm.username = '';
			vm.password = '';
			vm.remember = false;
			vm.error = '';
			vm.submit = submit;

			UserAuth.initLoginForm(function(){
				vm.submitDisabled = false;
			});

			function submit() {
				if(vm.form.$valid){
					vm.inLoading = true;
					UserAuth.login({
						username: vm.username,
						password: vm.password,
						remember: vm.remember,
						onSuccess: onLoginSuccess,
						onError: onLoginError
					});
				}
			}

			function onLoginSuccess() {
				vm.inLoading = false;
			}

			function onLoginError(event) {
				vm.inLoading = false;
				vm.error = event.response.error || 'Incorrect login or password.';
				vm.form.username.$dirty = true;
				vm.form.password.$dirty = true;
			}

		})($scope);

	}

})(angular);