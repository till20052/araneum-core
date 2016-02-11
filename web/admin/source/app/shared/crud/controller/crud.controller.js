(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDController', CRUDController);

    CRUDController.$inject = ['$scope', '$state', 'supervisor'];

    function CRUDController($scope, $state, supervisor) {
        $scope.icon = $state.$current.crud.icon;
        $scope.title = $state.$current.crud.title;

        $scope.form = {
            filter: {
                options: {
                    layout: 'cols',
                    controls: {
                        refresh: {
                            label: 'admin.general.RESET'
                        }
                    }
                }
            }
        };

        supervisor.loader.config
            .load($state.$current.initialize)
            .onLoaded({
                onSuccess: function (response) {
                    $scope.form.filter.data = response.filter;
                }
            });
    }

})();