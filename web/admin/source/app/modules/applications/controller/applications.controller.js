(function (ng) {
    'use strict';

    ng.module('app.applications')
        .controller('ApplicationsController', ApplicationsController);

    ApplicationsController.$inject = ['$scope'];
    function ApplicationsController($scope) {
        $scope.title = 'applications.APPLICATIONS';
        $scope.titleClass = 'icon-globe-alt mr';
    }
})
(angular);