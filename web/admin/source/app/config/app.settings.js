(function () {
    'use strict';

    angular
        .module('app')
        .run(settingsRun);

    settingsRun.$inject = ['$rootScope'];

    /**
     * Global settings
     *
     * @param $rootScope
     */
    function settingsRun($rootScope) {
        $rootScope.app = {
            name: 'Platform :: Araneum',
            description: 'Multisite manage tool',
            year: ((new Date()).getFullYear()),
            layout: {
                isFixed: true,
                isCollapsed: false,
                isBoxed: false,
                isRTL: false,
                horizontal: false,
                isFloat: false,
                asideHover: false,
                theme: null
            },
            useFullLayout: false,
            hiddenFooter: false,
            offsidebarOpen: false,
            asideToggled: false,
            viewAnimation: 'ng-fadeInUp'
        };
    }

})();