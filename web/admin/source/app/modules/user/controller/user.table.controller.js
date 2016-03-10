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
        $scope.title = 'admin.sidebar.nav.USERS';
        $scope.titleClass = 'icon-users mr';
    }

})();
