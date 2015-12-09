(function() {
	angular
		.module('app.formBuilder')
		.factory('formBuilderFactory', ['formBuildFiltersService', 'formBuildCreateUpdateService', function(formBuildFiltersService, formBuildCreateUpdateService) {
			return {
				builder: undefined,

				/**
				 * Get service with data
				 * @param {string} type type of form
				 * @returns {*} form builder service
				 */
				getBuilder: function(type) {
					if (type === 'filter') {
						this.builder = formBuildFiltersService;
					}

					if (type === 'create-update') {
						this.builder = formBuildCreateUpdateService;
					}

					return this.builder;
				}
			};
		}]);
})();
