(function (angular) {
	'use strict';

	angular
		.module('app.profile')
		.controller('ProfileEditCtrl', ['$scope', '$http', ProfileEditCtrl]);

	function ProfileEditCtrl($scope, $http) {

		$scope.inLoading = true;

		(function (viewModel) {

			var formFields = {};

			viewModel.errors = [];
			viewModel.save = save;

			$scope.inLoading = true;
			$http
				.get('/en/user/profile/edit')
				.success(function (response) {
					angular.forEach(response.form, function (field) {
						this[field.name] = field.value;
						formFields[field.name] = field;
					}, viewModel);
					$scope.inLoading = false;
				});

			function save() {
				$scope.inLoading = true;

				console.log(formFields);

				var data = {
					'btn_update_profile': 1
				};
				angular.forEach(formFields, function (field) {
					this[field.full_name] = viewModel[field.name]
				}, data);

				$http
					.post('/en/user/profile/edit', $.param(data), {
						headers: {'Content-Type': 'application/x-www-form-urlencoded'}
					})
					.success(function (response) {
						$scope.inLoading = false;
						$rootScope.user.name = response.username;
					})
					.error(function (response) {
						$scope.inLoading = false;
						viewModel.errors = response.errors;
					});
			}

		})($scope);

	}
})(angular);