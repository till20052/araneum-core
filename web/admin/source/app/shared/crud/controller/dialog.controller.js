(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDDialogController', CRUDDialogController);

    CRUDDialogController.$inject = ['$scope'];

    function CRUDDialogController($scope) {
        /* jshint -W004, validthis: true */
        var vm = this;

        $scope.isLoaded = false;
        $scope.icon = 'fa fa-file-o';
        $scope.title = 'Editor';
        $scope.form = {
            source: '',
            events: {
                wasCreated: onFormCreated,
                onSubmit: close
            },
            buttons: {
                cancel: {
                    icon: 'icon-ban',
                    label: 'admin.general.CANCEL',
                    click: close
                }
            }
        };

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
                        $scope[key] = key != 'form' ?
                            $scope.ngDialogData[key] :
                            angular.extend($scope[key], $scope.ngDialogData[key]);
                    }
                });
        }

        /**
         * Event of ngDialog Closing
         */
        function close() {
            $scope.closeThisDialog();
        }

        function onFormCreated(){
            $scope.isLoaded = true;
        }
    }

})();