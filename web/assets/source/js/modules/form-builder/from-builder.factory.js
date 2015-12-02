(function() {
    angular
        .module('app.formBuilder')
        .factory('formBuilderFactory', ['formBuildFiltersService', function(formBuildFiltersService) {
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

                    return this.builder;
                }
            };
        }]);
})();
