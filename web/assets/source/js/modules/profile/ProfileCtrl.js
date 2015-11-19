(function (angular) {

	'use strict';

	angular
		.module('app.profile')
		.controller('ProfileCtrl', ['$rootScope', '$scope', 'ngDialog', 'UserAuthService', ProfileCtrl]);

	function ProfileCtrl($rootScope, $scope, ngDialog, UserAuthService) {

		// Dashboard profile controller
		(function (viewModel) {
			viewModel.editProfile = editProfile;
			viewModel.logout = logout;

			$rootScope.user = {
				name: 'John',
				job: 'ng-developer',
				picture: '/assets/build/img/user/no-image.jpg'
			};

			UserAuthService.getAuthorizedUserData(function(response){
				console.log(response);
				$rootScope.user = response;
			});

			$rootScope.toggleUserBlock = function () {
				$rootScope.$broadcast('toggleUserBlock');
			};

			$rootScope.userBlockVisible = true;

			$rootScope.$on('toggleUserBlock', function (/*event, args*/) {
				$rootScope.userBlockVisible = !$rootScope.userBlockVisible;
			});

			function editProfile() {
				ngDialog.open({
					template: '/assets/build/html/ngdialog/profile.html',
					controller: 'ProfileEditCtrl'
				});
			}

			function logout() {
				$http
					.get('/en/logout')
					.success(function () {
						// will be algorithm to change layout state
					});
			}
		})($scope);

	}

})(angular);