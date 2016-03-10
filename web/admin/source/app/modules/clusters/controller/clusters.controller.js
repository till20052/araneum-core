(function (ng) {
    'use strict';

    ng.module('app.clusters')
        .controller('ClustersController', ClustersController);

    ClustersController.$inject = ['$scope'];
    function ClustersController($scope) {
        $scope.title = 'clusters.CLUSTERS';
        $scope.titleClass = 'icon-globe-alt mr';
    }
})
(angular);