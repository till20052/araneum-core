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
            'app.utils',
            'app.charts',
            'crud',

        /**
         * Modules
         */
            'app.users',
            'app.clusters',
            'app.locales',
            'app.dashboard',
            'app.applications',
            'app.leads',
            'app.emails'
        ]);
})();

