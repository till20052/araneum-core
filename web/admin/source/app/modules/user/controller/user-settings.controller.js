(function(angular){

    'use strict';

    angular
        .module('app.users')
        .controller('ProfileSettingsController', ['$scope', '$rootScope', 'User', ProfileSettingsController]);

    function ProfileSettingsController($scope, $rootScope, User){

        (function(vm){

            vm.layout = User.getSettings();
            $rootScope.app.layout = vm.layout;

            vm.$watch('layout', function () {
                User.setSettings(vm.layout);
            }, true);

        })($scope);

    }

})(angular);