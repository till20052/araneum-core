/**=========================================================
 * Module: config.js
 * App routes and resources configuration
 =========================================================*/


(function () {
	'use strict';

	angular
		.module('app')
		.config(routesConfig);

	routesConfig.$inject = ['$stateProvider', '$locationProvider', '$urlRouterProvider', 'RouteHelpersProvider'];
	function routesConfig($stateProvider, $locationProvider, $urlRouterProvider, helper) {

		// Set the following to true to enable the HTML5 Mode
		// You may have to set <base> tag in index and a routing configuration in your server
		$locationProvider.html5Mode(true);

		// defaults to authorization

		$urlRouterProvider.otherwise('/dashboard');

		//
		// Application Routes
		// -----------------------------------
		$stateProvider
			.state('app', {
				abstract: true,
				templateUrl: helper.basepath('app.html'),
				resolve: helper.resolveFor('fastclick', 'modernizr', 'icons', 'screenfull', 'animo', 'slimscroll', 'classyloader', 'toaster', 'whirl')
			});
	}

})();

