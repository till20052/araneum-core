(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDController', CRUDController);

    CRUDController.$inject = ['$scope'];

    function CRUDController($scope) {
        $scope.icon = 'icon-globe-alt';
        $scope.title = 'locales.LOCALES';
    }

})();