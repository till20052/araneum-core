(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDController', CRUDController);

    CRUDController.$inject = ['$scope', '$state', 'supervisor', 'FormTransformer'];

    /**
     * CRUD Controller
     *
     * @param $scope
     * @param $state
     * @param supervisor
     * @constructor
     */
    function CRUDController($scope, $state, supervisor, FormTransformer) {
        /* jshint validthis: true */
        var vm = this;

        $scope.icon = $state.$current.crud.icon;
        $scope.title = $state.$current.crud.title;

        $scope.form = {
            filter: {
                children: [],
                actions: {
                    search: {
                        icon: 'fa fa-search',
                        title: 'admin.general.SEARCH',
                        behavior: function () {
                            console.log('behavior on: filter.search', this);
                        }
                    },
                    refresh: {
                        icon: 'fa fa-refresh',
                        title: 'admin.general.RESET',
                        behavior: function () {
                            console.log('behavior on: filter.RESET', this);
                        }
                    }
                }
            }
        };

        vm.filter = {
            transformer: new FormTransformer('symfony-form-transformer'),
            view: {
                layout: 'grid'
            }
        };

        supervisor
            .loader('form')
            .load('/manage/locales/locale/1')
            .onLoaded({
                onSuccess: function (response) {
                    vm.filter.transform(response);
                }
            });
    }

})();