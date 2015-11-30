(function () {
    angular
        .module('app.formBuilderFactory', ['formBuildFiltersService', function(formBuildFiltersService) {
            return {
                builder: undefined,

                /**
                 * get service with data
                 * @param scope
                 * @returns {*} form builder service
                 */
                getBuilder: function(scope) {
                    if (scope.hasOwnProperty('filterFormData')) {
                        this.builder = formBuildFiltersService;
                        this.builder.setData(scope.filterFormData);
                    }

                    return this.builder;
                }
            };
        }])
})();