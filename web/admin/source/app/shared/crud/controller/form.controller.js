(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDFormController', CRUDFormController);

    CRUDFormController.$inject = ['$scope'];

    function CRUDFormController($scope) {
        /* jshint validthis: true */
        var vm = this,
            form = $scope.form;

        vm.form = {
            submit: submit,
            cancel: cancel
        };

        activate();

        /**
         * Activation
         *
         * @private
         */
        function activate() {

        }

        function submit() {
            form.submit(vm.form, {
                onSuccess: function () {
                    cancel();
                }
            });
        }

        function cancel() {
            if (
                $scope.$parent.hasOwnProperty('ngDialog') &&
                $scope.$parent.ngDialog instanceof Object
            ) {
                $scope.$parent.ngDialog.close();
            }
        }

    }

})();