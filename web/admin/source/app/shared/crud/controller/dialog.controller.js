(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDDialogController', CRUDDialogController);

    CRUDDialogController.$inject = ['$scope'];

    function CRUDDialogController($scope) {
        /* jshint validthis: true */
        var vm = this;

        $scope.isLoaded = false;
        $scope.icon = 'fa fa-file-o';
        $scope.title = 'Editor';
        $scope.form = {};

        vm.close = onClose;

        activate();

        /**
         * Activation
         */
        function activate() {
            ['icon', 'title', 'form']
                .forEach(function (key) {
                    if (
                        $scope.ngDialogData.hasOwnProperty(key) &&
                        $scope.ngDialogData[key].length !== 0
                    ) {
                        $scope[key] = $scope.ngDialogData[key];
                    }
                });
        }

        /**
         * Event of ngDialog Closing
         */
        function onClose() {
            $scope.closeThisDialog();
        }
    }

})();