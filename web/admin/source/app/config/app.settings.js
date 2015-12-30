(function () {
    'use strict';

    angular
        .module('app')
        .run(settingsRun);

    settingsRun.$inject = ['$rootScope', 'layout'];

    /**
     * Global settings
     *
     * @param $rootScope
     * @param layout
     */
    function settingsRun($rootScope, layout) {
        $rootScope.app = {
            name: 'Manage :: Araneum',
            description: 'Multisite manage tool',
            year: ((new Date()).getFullYear()),
            layout: layout,
            useFullLayout: false,
            hiddenFooter: false,
            offsidebarOpen: false,
            asideToggled: false,
            viewAnimation: 'ng-fadeInUp'
        };
    }

})();