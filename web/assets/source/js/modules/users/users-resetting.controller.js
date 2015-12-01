(function (angular) {
	'use strict';

	angular
		.module('app.users')
		.controller('ResettingController', ResettingController);

	ResettingController.$inject = ['$http', '$state', '$stateParams'];
	function ResettingController($http, $state, $stateParams) {

		// Initialize viewModel
		(function (vm) {

			vm.inLoading = false;
			vm.error = '';
			vm.username = '';
			vm.maskedEmail = '';

			vm.view = {
				request: true,
				checkMail: false,
				reset: false
			};

			if (typeof $stateParams.token != 'undefined') {
				vm.view.request = false;
				vm.view.reset = true;
			}

		})(this);

		// Initialize subView: Send Email
		(function (vm) {

			vm.sendEmailAction = sendEmailAction;

			function sendEmailAction() {
				var form = vm['request'];

				vm.error = '';

				if (form.$valid) {

					vm.inLoading = true;

					$http.post('/resetting/send-email', {
						username: vm.username
					}).then(function (response) {

						vm.inLoading = false;
						vm.view.request = false;
						vm.view.checkMail = true;
						vm.maskedEmail = response.data.email;

					}, function (response) {

						vm.inLoading = false;
						vm.error = response.data.error;

					});
				}
				else {
					form.username.$dirty = true;
				}
			}

		})(this);

		// Initialize subView: Reset
		(function (vm) {

			var url = '/manage/resetting/reset/' + $stateParams.token;
			var csrfToken = {};
			var fosUi = {
				first: {label: 'form.new_password', name: '', full_name: '', ng: '', value: ''},
				second: {label: 'form.new_password_confirmation', name: '', full_name: '', ng: '', value: ''}
			};

			vm.fosUi = fosUi;

			vm.onViewLoaded = onViewLoaded;
			vm.resetAction = resetAction;

			function onViewLoaded() {
				vm.inLoading = true;
				$http
					.post(url)
					.success(function (response) {
						angular.forEach(response, function(ui){
							if (ui.name == '_token') {
								csrfToken = ui;
								return;
							}
							this[ui.name] = angular.extend(this[ui.name], ui);
						}, fosUi);
						vm.inLoading = false;
					})
					.error(function (response) {
						vm.inLoading = false;
						vm.error = response.error
					});
			}

			function resetAction() {
				var data = {};

				vm.inLoading = true;

				angular.forEach(fosUi, function (ui) {
					this[ui.full_name] = ui.ng;
				}, data);

				data[csrfToken.full_name] = csrfToken.value;

				$http
					.post(
						url,
						$.param(data),
						{headers: {'Content-Type': 'application/x-www-form-urlencoded'}}
					)
					.success(function (response, statusCode) {
						if (statusCode != 202) {
							vm.inLoading = false;
							return;
						}
						$state.go('login');
					})
					.error(function (response) {
						vm.inLoading = false;
					});
			}

		})(this);

	}
})(angular);