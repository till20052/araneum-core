(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDDialogController', CRUDDialogController);

    CRUDDialogController.$inject = ['$scope', 'SweetAlert'];

    function CRUDDialogController($scope, SweetAlert) {
        /* jshint validthis: true */
        var vm = this;

        vm.icon = 'fa fa-file-o';
        vm.title = 'Editor';
        vm.form = null;

        vm.close = closeDialog;

        activate();

        /**
         * Activation
         */
        function activate() {
            ['icon', 'title', 'form'].forEach(function (key) {
                if (
                    $scope.ngDialogData.hasOwnProperty(key) &&
                    $scope.ngDialogData[key].length !== 0
                ) {
                    vm[key] = $scope.ngDialogData[key];
                }
            });
        }

        /**
         * Close Form Dialog
         */
        function closeDialog() {
            $scope.closeThisDialog();
        }
    }

})();