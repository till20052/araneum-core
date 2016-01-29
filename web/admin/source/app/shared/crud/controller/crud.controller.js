(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDController', CRUDController);

    CRUDController.$inject = ['$scope', 'CRUDConfigLoader', '$state'];

    function CRUDController($scope, CRUDConfigLoader, $state) {
        $scope.icon = 'icon-globe-alt';
        $scope.title = 'locales.LOCALES';

        CRUDConfigLoader
            .setUrl($state.$current.initialize)
            .load(function(response){
                $scope.filter = response.filter;
            });
    }

})();