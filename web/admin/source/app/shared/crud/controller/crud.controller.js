(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDController', CRUDController);

    CRUDController.$inject = ['$scope', 'CRUDConfigLoader', '$state'];

    function CRUDController($scope, CRUDConfigLoader, $state) {
        $scope.icon = 'icon-globe-alt';
        $scope.title = 'locales.LOCALES';

        $scope.filter =

        CRUDConfigLoader
            .setUrl($state.$current.initialize);
    }

})();