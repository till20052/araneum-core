(function () {
    angular.module('app.formBuilder')
        .factory('formBuildFiltersService', ['fromBuilderService', function (fromBuilderService) {
            var formBuilderFilters = fromBuilderService;

            /**
             * templates for inputs
             * @type {{text: string, datetime: string, choice: string}}
             */
            formBuilderFilters.templates = {
                text: '<div class=col-lg-6>' +
                '<div class="form-group">' +
                '<label for="input-id-1" class="col-sm-4 control-label"></label>' +
                '<div class="col-sm-8">' +
                '<input  type="text" class="form-control">' +
                '</div>' +
                '</div>' +
                '</div>',
                datetime: '<div class=col-lg-6>' +
                '<div class="form-group">' +
                '<label for="input-id-1" class="col-sm-4 control-label"></label>' +
                '<div class="col-sm-8">' +
                '<input  type="date" class="form-control">' +
                '</div>' +
                '</div>' +
                '</div>',
                choice: '<div class="col-lg-6">' +
                '<div class="form-group">' +
                '<label class="col-sm-4 control-label"></label>' +
                '<div class="col-sm-8">' +
                '<select chosen class="chosen-select input-md localytics-chosen"></select>' +
                '</div>' +
                '</div>' +
                '</div>'
            };

            return formBuilderFilters;
        }]);
})();
