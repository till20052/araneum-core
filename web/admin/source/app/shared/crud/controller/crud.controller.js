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
                },
                options: {
                    style: 'cols'
                }
            }
        };

        supervisor
            .loader('config')
            .load($state.$current.initialize)
            .onLoaded({
                onSuccess: function (response) {
                    if (!response.filter.hasOwnProperty('children') || !(response.filter.children instanceof Object))
                        return; // @todo need to create error handler
                    var children = response.filter.children;
                    $scope.form.filter.children = Object
                        .keys(children)
                        .map(function (key) {
                            /* jshint -W106 */
                            /** @type {{ block_prefixes: Array<String> }} */
                            var child = children[key].vars;
                            return angular.extend(child, {
                                type: child.block_prefixes[1]
                            });
                        });
                }
            });
    }

})();