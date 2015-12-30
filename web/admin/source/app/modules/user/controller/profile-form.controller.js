(function (angular) {
    'use strict';

    angular
        .module('app.users')
        .controller('ProfileEditController', ['$scope', '$http', 'User', ProfileEditController]);

    function ProfileEditController($scope, $http, User) {

        (function (vm) {

            var data;
            var formFields = {};

            vm.inLoading = true;
            vm.isSubmitDisabled = true;
            vm.errors = [];
            vm.username = '';
            vm.fullName = '';
            vm.email = '';

            vm.save = save;
            vm.close = close;

            $http
                .get('/user/profile/edit')
                .success(function (response) {
                    angular.forEach(response.form, function (field) {
                        this[field.name] = {
                            name: field.full_name,
                            value: field.value
                        };
                        if ($.inArray(field.name, ['username', 'fullName', 'email']) >= 0) {
                            vm[field.name] = field.value;
                        }
                    }, formFields);
                    vm.inLoading = vm.isSubmitDisabled = false;
                });

            function save() {

                if (vm.profile.$invalid) {
                    return;
                }

                vm.inLoading = true;

                angular.forEach(formFields, function (field, name) {
                    var value = vm[name];
                    if (typeof value == 'undefined') {
                        value = field.value;
                    }
                    this[field.name] = value;
                }, (data = {}));

                $http
                    .post('/user/profile/edit', $.param(data), {
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .success(onSaveSuccess)
                    .error(onSaveError);
            }

            function close(){
                vm.closeThisDialog();
            }

            function onSaveSuccess(response) {
                vm.inLoading = false;
                angular.forEach(response, function (value, name) {
                    if (name == 'fullName') {
                        name = 'name';
                    }
                    User[name] = value;
                });
                close();
            }

            function onSaveError(response) {
                vm.inLoading = false;
                vm.errors = response.errors;
            }

        })($scope);

    }
})(angular);