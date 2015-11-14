(function (angular) {
	'use strict';

	angular
		.module('app.listener')
		.provider('HTTPEventListener', HTTPEventListener);

	HTTPEventListener.$inject = [];
	function HTTPEventListener() {
		var events = {
			onRequest: [],
			onResponse: [],
			onError: []
		};

		return {
			onRequest: onRequest,
			onResponse: onResponse,
			onError: onError,
			$get: function () {
				return {
					getEventsByCase: getEventsByCase
				};
			}
		};

		function registerEvent(onCase, event) {
			if (typeof event == 'function') {
				events[onCase].push(event);
			}

			return this;
		}

		function onRequest(event) {
			return registerEvent('onRequest', event);
		}

		function onResponse(event) {
			return registerEvent('onResponse', event);
		}

		function onError(event) {
			return registerEvent('onError', event);
		}

		function getEventsByCase(onCase) {
			return events[onCase];
		}
	}
})(angular);
