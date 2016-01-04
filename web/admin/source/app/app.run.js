(function () {
    'use strict';

    angular
        .module('app')
        .run(appRun);

    appRun.$inject = ['$rootScope', '$state', '$stateParams', '$window', 'Colors'];

    /**
     * Run application
     *
     * @param $rootScope
     * @param $state
     * @param $stateParams
     * @param $window
     * @param Colors
     */
    function appRun($rootScope, $state, $stateParams, $window, Colors) {
        $rootScope.$state = $state;
        $rootScope.$stateParams = $stateParams;
        $rootScope.$storage = $window.localStorage;

        $rootScope.colorByName = Colors.byName;

        $rootScope.cancel = function ($event) {
            $event.stopPropagation();
        };

        $rootScope.$on('$stateNotFound', function (event, unfoundState) {
            console.log(unfoundState.to);
            console.log(unfoundState.toParams);
            console.log(unfoundState.options);
        });

        $rootScope.$on('$stateChangeSuccess', function () {
            $window.scrollTo(0, 0);
            $rootScope.currTitle = $state.current.title;
        });

        $rootScope.$on('$stateChangeError', function (event, toState, toParams, fromState, fromParams, error) {
            console.log(error);
        });

        $rootScope.currTitle = $state.current.title;
        $rootScope.pageTitle = function () {
            return document.title = $rootScope.app.name + ' - ' +
                ($rootScope.currTitle || $rootScope.app.description);
        };

        $rootScope.$on('toggleUserBlock', function () {
            $rootScope.userBlockVisible = !$rootScope.userBlockVisible;
        });
    }

})();
