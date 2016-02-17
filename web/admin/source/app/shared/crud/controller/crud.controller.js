(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDController', CRUDController);

    CRUDController.$inject = ['$scope', '$state', 'supervisor'];

    /**
     * CRUD Controller
     *
     * @param $scope
     * @param $state
     * @param supervisor
     * @constructor
     */
    function CRUDController($scope, $state, supervisor) {
        /* jshint validthis: true */
        var vm = this;

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

        supervisor
            .loader('config')
            .load($state.$current.initialize)
            .onLoaded({
                onSuccess: function (response) {
                    $scope.form.filter.data = response.filter;
                }
            });
    }

})();