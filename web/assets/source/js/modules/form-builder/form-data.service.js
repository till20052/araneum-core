(function() {
	angular
		.module('app.formBuilder')
		.factory('formDataService', ['$resource', function($resource) {
			var promise = undefined;
			return {
				/**
				 * Get data for form from server
				 * @param {string} url route with form data
				 * @returns {json} JSON
				 */
				setFromUrl: function(url) {
					promise = $resource(url, {}, {
						method: 'GET'
					}).get();
				},

				/**
				 * Return promise
				 * @returns {*|Function}
				 */
				getPromise: function() {
					return promise.$promise;
				}
			};
		}]);
})();
