(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDFormController', CRUDFormController);

    CRUDFormController.$inject = ['$scope', 'CRUDFormLoader', 'toaster'];

    function CRUDFormController($scope, CRUDFormLoader, toaster) {
        /* jshint validthis: true */
        var vm = this,
            CRUDForm = $scope.CRUDForm;

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
            if (CRUDForm instanceof Object) {
                CRUDFormLoader
                    .setUrl(CRUDForm.source)
                    .load({
                        onSuccess: function () {
                            CRUDFormLoader.clearPromise();
                        }
                    });
            }
        }

        function submit() {
            // @todo необхідно зробити валідацію форми
            CRUDForm.submit(vm.form, {
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