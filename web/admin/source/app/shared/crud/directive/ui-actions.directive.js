(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudUiActions', CRUDActionsDirective);

    CRUDActionsDirective.$inject = ['$compile', 'RouteHelpers'];

    function CRUDActionsDirective($compile, helper) {
        return {
            controller: ActionsController,
            controllerAs: 'vm',
            restrict: 'E',
            replace: true,
            templateUrl: helper.basepath('crud/actions.html')
        };
    }

    ActionsController.$inject = ['$scope', 'RouteHelpers'];

    function ActionsController($scope, helper){
        /* jshint validthis: true */
        var vm = this;

        vm.view = helper.basepath('crud/actions/'+$scope.view+'.html');
    }

})();