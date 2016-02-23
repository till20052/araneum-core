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
                    action: function (scope) {
                        vm.datatable.setAjaxSource([
                            vm.datatable.getAjaxSource().split('?')[0],
                            $.map(scope.form.data(), function (val, key) {
                                if (val === undefined)
                                    return;
                                return vm.filter.name + '[' + key + ']=' + val;
                            }).join('&')
                        ].join('?'));
                    }
                },
                refresh: {
                    icon: 'fa fa-refresh',
                    title: 'admin.general.RESET',
                    action: function (scope) {
                        scope.form.data({});
                        vm.datatable.setAjaxSource(vm.datatable.getAjaxSource().split('?')[0]);
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
                        .setAjaxSource(response.grid.source);
                }
            });
    }

})();