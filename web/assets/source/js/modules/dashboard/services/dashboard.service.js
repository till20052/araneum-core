(function (ng) {
	'use strict';

	ng.module('app.dashboard')
		.service('DashboardService', DashboardService);

	DashboardService.$inject = ['$http', '$q'];

	function DashboardService($http, $q) {
		var spinkitCss = '/assets/vendor/spinkit/css/spinkit.css';
		var dataSourceUrl = '/manage/dashboard/data-source.json';

		var stackCallback = [];

		return {
			appendSpinkit: appendSpinkit,
			onDataLoaded: onDataLoaded,
			loadData: loadData,
			refreshData: refreshData
		};

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

		function invokeHttpEvent(on, response, callback){
			ng.forEach(stackCallback, function(event){
				event[on](response);
			});
			if(typeof callback == 'function'){
				callback();
			}
		}

		function loadData(callback) {
			$http.get(dataSourceUrl)
				.then(function (response) {
					invokeHttpEvent('onSuccess', response, callback);
				}, function(error){
					invokeHttpEvent('onError', error, callback);
				});
			return this;
		}

		function refreshData(callback) {
			return loadData(callback);
		}

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