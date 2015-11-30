(function () {
    angular.module('app.formBuilder')
        .factory('formBuildFiltersService', ['fromBuilderService', function (fromBuilderService) {
            var formBuilderFilters = fromBuilderService;

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

            formBuilderFilters.buildForm = function () {
                console.log(this.elementsTemplateArray);
                var elMass = this.elementsTemplateArray;
                elMass.push('</div>');
                elMass.push('</div>');
                elMass.push('</div>');
                elMass.push('</div>');
                elMass.unshift('<div class=row>');
                elMass.unshift('<div class=col-lg10>');
                elMass.unshift('<div class=row>');
                elMass.unshift('<div class="panel-body pb">');
                this.fromTemplate = elMass.join(' ');

                return this.fromTemplate;
            };

            return formBuilderFilters;
        }])
})();