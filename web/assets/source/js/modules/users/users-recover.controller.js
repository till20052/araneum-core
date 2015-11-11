(function (angular) {
    'use strict';

    angular
        .module('app.users')
        .controller('RecoverPasswordController', RecoverPasswordController);

    RecoverPasswordController.$inject = ['$http'];
    function RecoverPasswordController($http) {
        var vm = this;

        init();

        function init() {

            vm.isLoading = false;
            vm.error = '';
            vm.username = 'till20052@gmail.com';

            vm.test = function () {
                console.log(vm.form);
            };

            vm.sendEmail = function () {

                vm.error = '';

                if (vm.form.$valid) {

                    vm.isLoading = true;

                    $http.post('/en/resetting/send-email', {
                        username: vm.username
                    }).then(function (response) {

                        vm.isLoading = false;

                        console.log(response);

                    }, function (response) {
                        vm.isLoading = false;
                        vm.error = response.data.error;
                    });
                }
                else {
                    vm.form.username.$dirty = true;
                }
            }

        }
    }
})(angular);