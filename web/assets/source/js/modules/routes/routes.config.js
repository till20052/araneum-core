/**=========================================================
 * Module: config.js
 * App routes and resources configuration
 =========================================================*/


(function() {
	'use strict';

	angular
		.module('app.routes')
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
			})
			.state('app.dashboard', {
				url: '/dashboard',
				title: 'Main page',
				templateUrl: helper.basepath('dashboard.html'),
				resolve: helper.resolveFor('flot-chart', 'flot-chart-plugins', 'chartjs', 'ngDialog'),
				controller: 'DashboardController'
			})
			.state('app.locales', {
				url: '/locales',
				initialize: '/manage/locales/init.json',
				templateUrl: helper.basepath('grid-template.html'),
				resolve: helper.resolveFor('datatables', 'whirl')
			})
			.state('app.table-ngtable', {
				url: '/table-ngtable',
				templateUrl: helper.basepath('table-ngtable.html'),
				resolve: angular.extend(helper.resolveFor('ngDialog', 'datatables', 'localytics.directives', 'oitozero.ngSweetAlert'), {
					tpl: function() {
						return {path: helper.basepath('ngdialog-template.html')};
					}
				})
			})
			.state('login', {
				url: '/login',
				title: 'Authorization',
				templateUrl: helper.basepath('users/login.html'),
				resolve: helper.resolveFor('whirl')
			})
			.state('resetting', {
				url: '/resetting',
				title: 'Recover',
				templateUrl: helper.basepath('users/resettingBase.html'),
				resolve: helper.resolveFor('whirl')
			})
			.state('reset', {
				url: '/resetting/reset/{token}',
				title: 'Recover',
				templateUrl: helper.basepath('users/resettingBase.html'),
				resolve: helper.resolveFor('whirl')
			});
	}

})();

