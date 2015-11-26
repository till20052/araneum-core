(function () {
	'use strict';

	angular
		.module('app.settings')
		.run(settingsRun);

	settingsRun.$inject = ['$rootScope'];

	function settingsRun($rootScope) {

		// Global Settings
		// -----------------------------------
		$rootScope.app = {
			name: 'Manage :: Araneum',
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
