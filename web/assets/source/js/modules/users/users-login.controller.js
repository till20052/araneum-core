(function (angular) {
    'use strict';

    angular
        .module('app.users')
        .controller('LoginUsersController', LoginUsersController);

    LoginUsersController.$inject = ['$http', '$state', 'UsersService'];
    function LoginUsersController($http, $state, UsersService) {
        var vm = this;

        activate();

        function activate() {
            // bind here all data from the form
            vm.account = {};
            // place the message if something goes wrong
            vm.authMsg = '';

            vm.setToken = function (token) {
                vm.token = token;
            };

            vm.login = function () {
                vm.authMsg = '';

                if (vm.loginForm.$valid) {

                    $http
                        .post('/en/login_check', {
                            _username: vm.account.username,
                            _password: vm.account.password,
                            _csrf_token: vm.token
                        })
                        .then(function (response) {
                            if (response.data.error) {
                                vm.authMsg = 'Incorrect login or password.';
                            } else {
                                $state.go('app.main');
                            }
                        }, function () {
                            vm.authMsg = 'Server Request Error';
                        });
                }
                else {
                    vm.loginForm.account_email.$dirty = true;
                    vm.loginForm.account_password.$dirty = true;
                }
            }

        }
    }
})(angular);