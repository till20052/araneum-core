/*!
 *
 * Arameum - Multisite manage tool
 *
 */

(function () {
    'use strict';

    angular
        .module('app', [
        /**
         * System components
         */
            'ngAnimate',
            'ngStorage',
            'ngDialog',
            'ngCookies',
            'pascalprecht.translate',
            'ui.bootstrap',
            'cfp.loadingBar',
            'ngSanitize',
            'ngResource',
            'tmh.dynamicLocale',
            'ui.utils',

        /**
         * Shared components
         */
            'app.router',
            'app.layout',
            'app.panels',
            'app.listener',
            'app.sidebar',
            'app.navsearch',
            'app.preloader',
            'app.loadingbar',
            'app.translate',
            'app.formBuilder',
            'app.utils',
            'app.charts',
            'app.action-builder',

        /**
         * Modules
         */
            'app.users',
            'app.locales',
            'app.dashboard'
        ]);
})();

