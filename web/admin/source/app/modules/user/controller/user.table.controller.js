(function () {
    'use strict';

    angular
        .module('app.users')
        .controller('UserTableController', UserTableController);

    UserTableController.$inject = ['$scope'];

    /**
     * @param $scope
     */
    function UserTableController($scope) {
        $scope.title = 'locales.LOCALES';
        $scope.titleClass = 'icon-users mr';
    }

})();
