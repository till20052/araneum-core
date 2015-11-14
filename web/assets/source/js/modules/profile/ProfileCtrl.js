(function(angular){

	'use strict';

	angular
		.module('app.profile')
		.controller('ProfileCtrl', ['$rootScope', '$scope', 'ngDialog', ProfileCtrl]);

	function ProfileCtrl($rootScope, $scope, ngDialog){

		// Dashboard profile controller
		(function (viewModel) {
			viewModel.editProfile = editProfile;

			$rootScope.user = {
				name: 'John',
				job: 'ng-developer',
				picture: '/assets/build/img/user/02.jpg'
			};

			$rootScope.toggleUserBlock = function () {
				$rootScope.$broadcast('toggleUserBlock');
			};

			$rootScope.userBlockVisible = true;

			$rootScope.$on('toggleUserBlock', function (/*event, args*/) {
				$rootScope.userBlockVisible = !$rootScope.userBlockVisible;
			});

			function editProfile(){
				ngDialog.open({
					template: '/assets/build/html/ngdialog/profile.html',
					controller: 'ProfileEditCtrl'
				});
			}
		})($scope);

	}

})(angular);