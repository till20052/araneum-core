(function (angular) {

    'use strict';

    angular
        .module('app.users')
        .controller('ProfileController', ['$rootScope', '$scope', 'ngDialog', 'User', 'UserAuth', 'RouteHelpers', ProfileController]);

    function ProfileController($rootScope, $scope, ngDialog, User, UserAuth, helper) {

        (function (vm) {

            vm.inLoading = false;
            vm.user = User;

            vm.editProfile = editProfile;
            vm.logout = logout;

            $rootScope.toggleUserBlock = function () {
                $rootScope.$broadcast('toggleUserBlock');
            };

            $rootScope.userBlockVisible = true;

            /**
             * Edit profile
             */
            function editProfile() {
                ngDialog.open({
                    template: helper.basepath('ngdialog/profile.html'),
                    controller: 'ProfileEditController'
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