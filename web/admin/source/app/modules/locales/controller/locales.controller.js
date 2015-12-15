(function (ng) {
    'use strict';

    ng.module('app.locales')
        .controller('LocalesController', LocalesController);

    LocalesController.$inject = ['$scope'];
    function LocalesController($scope) {
        $scope.title = 'locales.LOCALES';
        $scope.titleClass = 'icon-globe-alt mr';
    }
})
(angular);