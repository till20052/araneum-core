(function (angular) {
    'use strict';

    angular
        .module('app.users')
        .controller('RecoverPasswordController', RecoverPasswordController);

    RecoverPasswordController.$inject = ['$http', '$stateParams'];
    function RecoverPasswordController($http, $stateParams) {
        var vm = this;

        init();

        function init() {

            vm.view = {
                request: true,
                checkMail: false,
                reset: false
            };

            if(typeof $stateParams.token != 'undefined'){
                vm.view.request = false;
                vm.view.reset = true;
            }

            vm.isLoading = false;
            vm.error = '';
            vm.username = 'till20052@gmail.com';
            vm.maskedEmail = '';

            vm.sendEmail = function () {

                vm.error = '';

                if (vm.form.$valid) {

                    vm.isLoading = true;

                    $http.post('/en/resetting/send-email', {
                        username: vm.username
                    }).then(function (response) {

                        vm.isLoading = false;
                        vm.view.request = false;
                        vm.view.checkMail = true;
                        vm.maskedEmail = response.data.email;

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