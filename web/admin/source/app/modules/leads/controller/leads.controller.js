(function (ng) {
    'use strict';

    ng.module('app.leads')
        .controller('LeadsController', LeadsController);

    LeadsController.$inject = ['$scope'];
    function LeadsController($scope) {
        $scope.title = 'leads.LEADS';
        $scope.titleClass = 'icon-globe-alt mr';
    }
})
(angular);