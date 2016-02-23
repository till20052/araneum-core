(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDController', CRUDController);

    CRUDController.$inject = ['$scope', '$state', 'supervisor', 'FormTransformer', 'Layout'];

    /**
     * CRUD Controller
     *
     * @constructor
     */
    function CRUDController($scope, $state, supervisor, FormTransformer, Layout) {
        /* jshint validthis: true */
        var vm = this;

        $scope.icon = $state.$current.crud.icon;
        $scope.title = $state.$current.crud.title;

        vm.filter = {
            actions: {
                search: {
                    icon: 'fa fa-search',
                    title: 'admin.general.SEARCH',
                    class: 'primary',
                    action: function () {
                        console.log('search');
                    }
                },
                refresh: {
                    icon: 'fa fa-refresh',
                    title: 'admin.general.RESET',
                    action: function () {
                        console.log('refresh');
                    }
                }
            },
            transformer: new FormTransformer('symfony-form-transformer'),
            view: {
                layout: new Layout('grid', 2)
            }
        };

        vm.datatable = {};

        supervisor
            .loader('config')
            .load($state.$current.initialize)
            .onLoaded({
                onSuccess: function (response) {
                    vm.filter.transform(response.filter);
                    vm.datatable.setColumns(response.grid.columns)
                        .setSource(response.grid.source);
                }
            });
    }

})();