(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDController', CRUDController);

    CRUDController.$inject = ['$scope', 'CRUDConfigLoader', '$state'];

    function CRUDController($scope, CRUDConfigLoader, $state) {
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

        CRUDConfigLoader
            .setUrl($state.$current.initialize)
            .load({
                onSuccess: function (response) {
                    $scope.form.filter.data = response.filter;
                }
            });
    }

})();