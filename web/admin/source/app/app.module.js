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
			'ngRoute',
			'ngAnimate',
			'ngStorage',
			'ngDialog',
			'ngCookies',
			'pascalprecht.translate',
			'ui.bootstrap',
			'ui.router',
			'oc.lazyLoad',
			'cfp.loadingBar',
			'ngSanitize',
			'ngResource',
			'tmh.dynamicLocale',
			'ui.utils',

			/**
			 * Shared components
			 */
			'app.listener',
			'app.lazyload',
			'app.sidebar',
			'app.navsearch',
			'app.preloader',
			'app.loadingbar',
			'app.translate',
			'app.formBuilder',
			'app.utils',

			/**
			 * Modules
			 */
			'app.users',
			'app.locales',
			'app.dashboard'
		]);
})();

