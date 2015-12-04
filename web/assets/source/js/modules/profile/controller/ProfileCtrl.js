(function (angular) {

	'use strict';

	angular
		.module('app.profile')
		.controller('ProfileCtrl', ['$rootScope', '$scope', 'ngDialog', 'User', 'UserAuth', ProfileCtrl]);

	function ProfileCtrl($rootScope, $scope, ngDialog, User, UserAuth) {

		(function (vm) {

			vm.inLoading = false;
			vm.user = User.getUser();

			vm.editProfile = editProfile;
			vm.logout = logout;

			$rootScope.userBlockVisible = true;

			function editProfile() {
				ngDialog.open({
					template: '/assets/build/html/ngdialog/profile.html',
					controller: 'ProfileEditCtrl'
				});
			}

			function logout() {
				vm.inLoading = true;
				UserAuth.logout({
					onSuccess: function(){
						vm.inLoading = false;
					}
				});
			}

		})($scope);

	}

})(angular);