(function () {
    'use strict';

    angular
        .module('app.users')
        .run(userRun);

    userRun.$inject = ['$rootScope'];

    /**
     * Run user application
     */
    function userRun($rootScope) {
        $rootScope.$on('$stateChangeStart', function (event, toState) {
            //UserAuth.onStartChangeState(event, toState);
        });

        //
    }

})();