(function (ng) {
	'use strict';

	ng.module('app.dashboard')
		.service('DashboardService', DashboardService);

	DashboardService.$inject = ['$http', 'APP_COLORS'];

	/**
	 * Dashboard Service
	 *
	 * @param $http
	 * @param $q
	 * @returns {{appendSpinkit: appendSpinkit, onDataLoaded: onDataLoaded, loadData: loadData, refreshData: refreshData}}
	 * @constructor
	 */
	function DashboardService($http, APP_COLORS) {
		var spinkitCss = '/admin/vendor/spinkit/css/spinkit.css';
		var dataSourceUrl = '/manage/dashboard/data-source.json';

		var stackCallback = [];
		var colorsByLabels = {};
		var colors = Object.keys(APP_COLORS).map(function(key){
			return APP_COLORS[key];
		});

		return {
			appendSpinkit: appendSpinkit,
			onDataLoaded: onDataLoaded,
			loadData: loadData,
			refreshData: refreshData,
			assignColorsByLabel: assignColorsByLabel
		};

		function assignColorsByLabel(data) {
			ng.forEach(data, function (item, i) {
				if (colorsByLabels[item.label] == undefined) {
					colorsByLabels[item.label] = colors[Object.keys(colorsByLabels).length];
				}
				this[i].color = colorsByLabels[item.label];
			}, data);
			return data;
		}

		/**
		 * Set on success/error callbacks in callback stack
		 * @param onSuccess
		 * @param onError
		 * @returns {onDataLoaded}
		 */
		function onDataLoaded(onSuccess, onError) {
			stackCallback.push({
				onSuccess: typeof onSuccess != 'function'
					? function () {}
					: onSuccess,
				onError: typeof onError != 'function'
					? function () {}
					: onError
			});

			return this;
		}

		/**
		 * Private method to invoke all callbacks
		 * @param on
		 * @param response
		 * @param callback
		 */
		function invokeHttpEvent(on, response, callback) {
			ng.forEach(stackCallback, function (event) {
				event[on](response);
			});
			if (typeof callback == 'function') {
				callback();
			}
		}

		/**
		 * Load data from server
		 * @param callback
		 * @returns {loadData}
		 */
		function loadData(callback) {
			$http.get(dataSourceUrl)
				.then(function (response) {
					invokeHttpEvent('onSuccess', response, callback);
				}, function (error) {
					invokeHttpEvent('onError', error, callback);
				});
			return this;
		}

		/**
		 * Refresh data from server
		 * @param callback
		 * @returns {loadData}
		 */
		function refreshData(callback) {
			return loadData(callback);
		}

		/**
		 * Init spinkit
		 * @returns {appendSpinkit}
		 */
		function appendSpinkit() {
			if (!$('link[href="' + spinkitCss + '"]').length > 0) {
				ng.element('head')
					.append(
						$('<link />')
							.attr({
								rel: 'stylesheet',
								type: 'text/css',
								href: spinkitCss
							})
					);
			}

			return this;
		}
	}

})(angular);