(function () {
    'use strict';

    angular
        .module('app.users')
        .run(userRun);

    userRun.$inject = ['$rootScope', 'UserAuth'];

    /**
     * Run user application
     */
    function userRun($rootScope, Auth) {
        $rootScope.$on('$stateChangeStart', function (event, toState) {
            Auth.onStartChangeState(event, toState);
        });
    }

})();