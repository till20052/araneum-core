(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDController', CRUDController);

    CRUDController.$inject = ['$scope', 'CRUDConfigLoader', '$state'];

    function CRUDController($scope, CRUDConfigLoader, $state) {
        $scope.icon = 'icon-globe-alt';
        $scope.title = 'locales.LOCALES';

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