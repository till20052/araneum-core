(function(angular){

	'use strict';

	angular
		.module('app.profile')
		.controller('ProfileSettingsCtrl', ['$scope', '$rootScope', 'User', ProfileSettingsCtrl]);

	function ProfileSettingsCtrl($scope, $rootScope, User){

		(function(vm){

			vm.layout = User.getSettings();
			$rootScope.app.layout = vm.layout;

			vm.$watch('layout', function () {
				User.setSettings(vm.layout);
			}, true);

		})($scope);

	}

})(angular);