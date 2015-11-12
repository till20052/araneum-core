(function (angular) {
    'use strict';

    angular
        .module('app.users')
        .controller('RecoverPasswordController', RecoverPasswordController);

    RecoverPasswordController.$inject = ['$http', '$stateParams'];
    function RecoverPasswordController($http, $stateParams) {

        // Initialize viewController
        (function (viewController) {

            viewController.sendEmail = sendEmail;

            viewController.isLoading = false;
            viewController.error = '';
            viewController.username = 'till20052@gmail.com';
            viewController.maskedEmail = '';

            viewController.view = {
                request: true,
                checkMail: false,
                reset: false
            };

            if (typeof $stateParams.token != 'undefined') {
                viewController.view.request = false;
                viewController.view.reset = true;
            }

            function sendEmail() {
                viewController.error = '';

                if (viewController.form.$valid) {

                    viewController.isLoading = true;

                    $http.post('/en/resetting/send-email', {
                        username: this.username
                    }).then(function (response) {

                        viewController.isLoading = false;
                        viewController.view.request = false;
                        viewController.view.checkMail = true;
                        viewController.maskedEmail = response.data.email;

                    }, function (response) {
                        viewController.isLoading = false;
                        viewController.error = response.data.error;
                    });
                }
                else {
                    this.form.username.$dirty = true;
                }
            };

        })(this);

    }
})(angular);