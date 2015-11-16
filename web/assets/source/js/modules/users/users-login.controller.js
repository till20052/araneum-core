(function (angular) {
    'use strict';

    angular
        .module('app.users')
        .controller('LoginController', LoginController);

    LoginController.$inject = ['$http', '$state'];
    function LoginController($http, $state) {
        var vm = this;

        activate();

        function activate() {
            // bind here all data from the form
            vm.account = {};
            // place the message if something goes wrong
            vm.authMsg = '';
            //
            vm.CSRFToken = '';

            vm.inLoading = false;

            vm.setCSRFToken = function (CSRFToken) {
                vm.CSRFToken = CSRFToken;
            };

            vm.login = function () {
                vm.authMsg = '';

                if (vm.loginForm.$valid) {
					vm.inLoading = true;
                    $http
                        .post('/en/login_check', {
                            _username: vm.account.username,
                            _password: vm.account.password,
                            _csrf_token: vm.CSRFToken
                        })
                        .then(function (response) {
                            if (response.data.error) {
                                vm.authMsg = 'Incorrect login or password.';
                            } else {
                                $state.go('app.main');
                            }
							vm.inLoading = false;
                        }, function () {
							vm.inLoading = false;
                            vm.authMsg = 'Server Request Error';
                        });
                }
                else {
                    vm.loginForm.account_email.$dirty = true;
                    vm.loginForm.account_password.$dirty = true;
                }
            };

        }
    }
})(angular);